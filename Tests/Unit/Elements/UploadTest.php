<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Tests\Unit\Elements;

use Ameos\AmeosForm\Elements\Upload;
use Ameos\AmeosForm\Exception\BadConfigurationException;
use Ameos\AmeosForm\Tests\Unit\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Core\Environment;

final class UploadTest extends AbstractUnitTestCase
{
    #[Test]
    public function directoryIsRequired(): void
    {
        $this->expectException(BadConfigurationException::class);
        new Upload('tx_test[file]', 'file', [], $this->createFormStub());
    }

    #[Test]
    public function directoriesAreResolved(): void
    {
        $element = new Upload('tx_test[file]', 'file', ['directory' => '/fileadmin/uploads/'], $this->createFormStub());

        self::assertSame(Environment::getPublicPath() . '/fileadmin/uploads/', $element->getUploadDirectory());
        self::assertSame('fileadmin/uploads/', $element->getUploadDirectory(false));
        self::assertSame(Environment::getPublicPath() . '/typo3temp/ameos_form/tempupload/', $element->getTemporaryDirectory());
        self::assertDirectoryExists($element->getTemporaryDirectory());
    }

    #[Test]
    public function temporaryFileNamesAreRestrictedToExistingTemporaryFiles(): void
    {
        $element = new Upload('tx_test[file]', 'file', ['directory' => 'fileadmin/uploads'], $this->createFormStub());
        $originalName = 'unittest_' . bin2hex(random_bytes(4)) . '.txt';
        $fileName = $element->createTemporaryFileName($originalName);
        file_put_contents($element->getTemporaryDirectory() . $fileName, 'x');
        $this->testFilesToDelete[] = $element->getTemporaryDirectory() . $fileName;
        // existing file without the random prefix (e.g. uploaded by another visitor and guessed)
        file_put_contents($element->getTemporaryDirectory() . $originalName, 'x');
        $this->testFilesToDelete[] = $element->getTemporaryDirectory() . $originalName;

        self::assertMatchesRegularExpression('/^[0-9a-f]{16}_' . preg_quote($originalName, '/') . '$/', $fileName);
        self::assertSame($originalName, $element->getOriginalFileName($fileName));
        self::assertSame($originalName, $element->getOriginalFileName($originalName));
        self::assertSame(
            [$fileName],
            $element->filterTemporaryFileNames([$fileName, $originalName, '../../../config/system/settings.php', 'missing.txt', '', ['array']]),
        );
    }

    #[Test]
    public function temporaryFileCanBeDeleted(): void
    {
        $element = new Upload('tx_test[file]', 'file', ['directory' => 'fileadmin/uploads'], $this->createFormStub());
        $fileName = $element->createTemporaryFileName('unittest_' . bin2hex(random_bytes(4)) . '.html');
        file_put_contents($element->getTemporaryDirectory() . $fileName, 'x');

        $element->deleteTemporaryFile('../../../index.php');
        $element->deleteTemporaryFile($fileName);

        self::assertFileDoesNotExist($element->getTemporaryDirectory() . $fileName);
    }

    #[Test]
    public function fileNamesCanBeRemovedFromValue(): void
    {
        $element = new Upload('tx_test[file]', 'file', ['directory' => 'fileadmin/uploads'], $this->createFormStub());
        $element->setValue(['a.pdf', 'b.exe', 'c.pdf']);
        $element->removeFileFromValue('b.exe');

        self::assertSame(['a.pdf', 'c.pdf'], $element->getFileNames());
    }

    #[Test]
    public function optionsAreRead(): void
    {
        $element = new Upload(
            'tx_test[file]',
            'file',
            ['directory' => 'fileadmin', 'multiple' => 1, 'canoverwrite' => 1, 'show_link' => 0, 'filename' => '../cv.pdf'],
            $this->createFormStub(),
        );

        self::assertTrue($element->isMultiple());
        self::assertTrue($element->canOverwrite());
        self::assertFalse($element->showLink());
        self::assertSame('cv.pdf', $element->getForcedFilename());
        self::assertFalse($element->isSearchable());
        self::assertSame(Upload::STATE_EMPTY, $element->getState());
    }

    #[Test]
    public function pendingFilesAreRenderedEscaped(): void
    {
        $this->stubTranslations();
        $element = new Upload('tx_test[file]', 'file', ['directory' => 'fileadmin'], $this->createForm());
        $element->setValue(['<img src=x onerror=alert(1)>.png']);
        $element->updateState(Upload::STATE_PENDING);
        $html = $element->toHtml();

        self::assertStringNotContainsString('<img', $html);
        self::assertStringContainsString('&lt;img src=x onerror=alert(1)&gt;.png', $html);
        self::assertStringContainsString('name="tx_test[file][temporary][]"', $html);
        self::assertStringContainsString('<input type="file" id="tx_test_file-upload" name="tx_test[file][upload][]" />', $html);
    }
}
