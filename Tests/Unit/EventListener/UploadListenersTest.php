<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Tests\Unit\EventListener;

use Ameos\AmeosForm\Elements\Text;
use Ameos\AmeosForm\Elements\Upload;
use Ameos\AmeosForm\Event\BindValueFromRequestEvent;
use Ameos\AmeosForm\Event\ValidFormEvent;
use Ameos\AmeosForm\EventListener\MoveTemporaryFile;
use Ameos\AmeosForm\EventListener\UploadTemporaryFile;
use Ameos\AmeosForm\Form\Form;
use Ameos\AmeosForm\Tests\Unit\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Http\UploadedFile;
use TYPO3\CMS\Core\Resource\Security\FileNameValidator;

final class UploadListenersTest extends AbstractUnitTestCase
{
    private string $prefix;
    /** @var list<string> files removed after the test if they still exist */
    private array $filesToCleanUp = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->prefix = 'unittest_' . bin2hex(random_bytes(4)) . '_';
    }

    protected function tearDown(): void
    {
        foreach ($this->filesToCleanUp as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        parent::tearDown();
    }

    #[Test]
    public function uploadedFileIsStoredInTemporaryDirectory(): void
    {
        [$form, $element] = $this->createUploadElement();
        $event = new BindValueFromRequestEvent($form, $element, ['upload' => [$this->createUploadedFile('cv.pdf', 'pdf content')], 'temporary' => []]);

        $this->createListener()($event);

        $fileName = $this->assertTemporaryFileNames(['cv.pdf'], $event->getValue())[0];
        self::assertSame(Upload::STATE_PENDING, $element->getState());
        self::assertFileExists($element->getTemporaryDirectory() . $fileName);
        $this->testFilesToDelete[] = $element->getTemporaryDirectory() . $fileName;
    }

    #[Test]
    public function sameFileNameUploadedTwiceGivesTwoTemporaryFiles(): void
    {
        [$form, $element] = $this->createUploadElement();
        $values = [];
        foreach (['old', 'new'] as $content) {
            $event = new BindValueFromRequestEvent($form, $element, ['upload' => [$this->createUploadedFile('cv.pdf', $content)]]);
            $this->createListener()($event);
            $values[] = $this->assertTemporaryFileNames(['cv.pdf'], $event->getValue())[0];
        }

        self::assertNotSame($values[0], $values[1]);
        self::assertSame('old', file_get_contents($element->getTemporaryDirectory() . $values[0]));
        self::assertSame('new', file_get_contents($element->getTemporaryDirectory() . $values[1]));
        foreach ($values as $fileName) {
            $this->testFilesToDelete[] = $element->getTemporaryDirectory() . $fileName;
        }
    }

    #[Test]
    public function tooLargeFileIsRejected(): void
    {
        $this->stubTranslations();
        [$form, $element] = $this->createUploadElement(['maxSize' => '1K']);
        $event = new BindValueFromRequestEvent($form, $element, ['upload' => [$this->createUploadedFile('big.pdf', str_repeat('x', 2048))]]);

        $this->createListener()($event);

        self::assertSame([], $event->getValue());
        self::assertSame(Upload::STATE_EMPTY, $element->getState());
        self::assertSame(['The size of the file "' . $this->prefix . 'big.pdf" exceeds the allowed limit of 1K.'], $form->getErrorManager()->getErrorsFor($element));
    }

    #[Test]
    public function maxSizeInBytesIsSupported(): void
    {
        [$form, $element] = $this->createUploadElement(['maxSize' => '4096']);
        $event = new BindValueFromRequestEvent($form, $element, ['upload' => [$this->createUploadedFile('small.pdf', str_repeat('x', 2048))]]);

        $this->createListener()($event);

        $fileName = $this->assertTemporaryFileNames(['small.pdf'], $event->getValue())[0];
        $this->testFilesToDelete[] = $element->getTemporaryDirectory() . $fileName;
    }

    #[Test]
    public function deniedFileNameIsRejected(): void
    {
        $this->stubTranslations();
        [$form, $element] = $this->createUploadElement();
        $event = new BindValueFromRequestEvent($form, $element, ['upload' => [$this->createUploadedFile('shell.php', '<?php echo 1;')]]);

        $this->createListener()($event);

        self::assertSame([], $event->getValue());
        self::assertFileDoesNotExist($element->getTemporaryDirectory() . $this->prefix . 'shell.php');
        self::assertSame(['The file "' . $this->prefix . 'shell.php" is not allowed.'], $form->getErrorManager()->getErrorsFor($element));
    }

    #[Test]
    public function temporaryFilesAreKeptAndSanitized(): void
    {
        [$form, $element] = $this->createUploadElement();
        $fileName = $element->createTemporaryFileName($this->prefix . 'kept.pdf');
        $temporary = $element->getTemporaryDirectory() . $fileName;
        file_put_contents($temporary, 'x');
        $this->testFilesToDelete[] = $temporary;
        $event = new BindValueFromRequestEvent($form, $element, [
            'upload' => [],
            'temporary' => [$fileName, '../../../../config/system/settings.php'],
        ]);

        $this->createListener()($event);

        self::assertSame([$fileName], $event->getValue());
        self::assertSame(Upload::STATE_PENDING, $element->getState());
    }

    #[Test]
    public function otherElementsAreIgnored(): void
    {
        $form = $this->createFormStub();
        $event = new BindValueFromRequestEvent($form, new Text('tx_test[t]', 't', [], $form), 'value');

        $this->createListener()($event);

        self::assertSame('value', $event->getValue());
    }

    #[Test]
    public function pendingFilesAreMovedToUploadDirectory(): void
    {
        $directory = 'typo3temp/' . $this->prefix . 'uploads';
        [$form, $element] = $this->createUploadElement(['directory' => $directory]);
        $this->testFilesToDelete[] = Environment::getPublicPath() . '/' . $directory;
        $fileName = $element->createTemporaryFileName('cv.pdf');
        $temporary = $element->getTemporaryDirectory() . $fileName;
        file_put_contents($temporary, 'x');
        $element->setValue([$fileName, '../../evil.php']);
        $element->updateState(Upload::STATE_PENDING);

        (new MoveTemporaryFile())(new ValidFormEvent($form));
        // moved with the original file name
        self::assertSame(['cv.pdf'], $element->getValue());
        self::assertSame(Upload::STATE_DONE, $element->getState());
        self::assertFileExists($element->getUploadDirectory() . 'cv.pdf');
        self::assertFileDoesNotExist($temporary);

        // Same name again: renamed, not overwritten
        file_put_contents($temporary, 'y');
        $element->setValue([$fileName]);
        $element->updateState(Upload::STATE_PENDING);
        (new MoveTemporaryFile())(new ValidFormEvent($form));
        self::assertSame(['cv_1.pdf'], $element->getValue());
    }

    #[Test]
    public function forcedFileNameIsUsed(): void
    {
        $directory = 'typo3temp/' . $this->prefix . 'forced';
        [$form, $element] = $this->createUploadElement(['directory' => $directory, 'filename' => 'photo.jpg']);
        $this->testFilesToDelete[] = Environment::getPublicPath() . '/' . $directory;
        $fileName = $element->createTemporaryFileName('img.jpg');
        file_put_contents($element->getTemporaryDirectory() . $fileName, 'x');
        $element->setValue([$fileName]);
        $element->updateState(Upload::STATE_PENDING);

        (new MoveTemporaryFile())(new ValidFormEvent($form));

        self::assertSame(['photo.jpg'], $element->getValue());
        self::assertFileExists($element->getUploadDirectory() . 'photo.jpg');
    }

    /**
     * @param list<string> $originalNames
     * @return list<string>
     */
    private function assertTemporaryFileNames(array $originalNames, mixed $value): array
    {
        self::assertIsArray($value);
        self::assertCount(count($originalNames), $value);
        foreach (array_values($value) as $index => $fileName) {
            self::assertIsString($fileName);
            self::assertMatchesRegularExpression('/^[0-9a-f]{16}_' . preg_quote($this->prefix . $originalNames[$index], '/') . '$/', $fileName);
        }
        /** @var list<string> $value */
        return $value;
    }

    private function createListener(): UploadTemporaryFile
    {
        return new UploadTemporaryFile(new FileNameValidator());
    }

    /**
     * @param array<string, mixed> $configuration
     * @return array{Form, Upload}
     */
    private function createUploadElement(array $configuration = []): array
    {
        $form = $this->createForm();
        $form->add('file', 'upload', $configuration + ['directory' => 'fileadmin/test']);
        return [$form, $this->element($form, 'file', Upload::class)];
    }

    private function createUploadedFile(string $name, string $content): UploadedFile
    {
        $path = Environment::getVarPath() . '/transient/' . $this->prefix . bin2hex(random_bytes(4));
        @mkdir(dirname($path), 0777, true);
        file_put_contents($path, $content);
        $this->filesToCleanUp[] = $path;
        return new class ($path, strlen($content), UPLOAD_ERR_OK, $this->prefix . $name) extends UploadedFile {
            // The original only accepts files uploaded through HTTP (move_uploaded_file)
            public function moveTo($targetPath): void
            {
                rename((string) $this->getTemporaryFileName(), (string) $targetPath);
            }
        };
    }
}
