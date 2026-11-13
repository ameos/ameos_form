<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Tests\Unit;

use Ameos\AmeosForm\Elements\Checkbox;
use Ameos\AmeosForm\Elements\Radio;
use Ameos\AmeosForm\Elements\Upload;
use Ameos\AmeosForm\Utility\FileNameUtility;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

/**
 * Findings of the security review
 */
final class SecurityTest extends AbstractUnitTestCase
{
    #[Test]
    public function choiceLabelsAreEscapedByDefault(): void
    {
        $items = ['a' => '<img src=x onerror=alert(1)>'];
        $radio = new Radio('tx_test[r]', 'r', ['items' => $items], $this->createFormStub());
        $checkbox = new Checkbox('tx_test[c]', 'c', ['items' => $items], $this->createFormStub());

        self::assertStringNotContainsString('<img', $radio->toHtml());
        self::assertStringNotContainsString('<img', $checkbox->toHtml());
        self::assertStringNotContainsString('<img', $radio->getRenderingInformation()['choices']['a']['label']);
    }

    #[Test]
    public function htmlLabelsMustBeEnabledExplicitly(): void
    {
        $radio = new Radio('tx_test[r]', 'r', ['items' => ['a' => 'I accept the <a href="/terms">terms</a>'], 'htmllabel' => true], $this->createFormStub());

        self::assertStringContainsString('<a href="/terms">terms</a>', $radio->toHtml());
    }

    #[Test]
    public function pendingFilesAreNotLinked(): void
    {
        $this->stubTranslations();
        $element = $this->element($this->createForm()->add('file', 'upload', ['directory' => 'fileadmin/test']), 'file', Upload::class);
        $fileName = $element->createTemporaryFileName('cv.pdf');
        $element->setValue([$fileName]);
        $element->updateState(Upload::STATE_PENDING);

        $html = $element->toHtml();
        $data = $element->getRenderingInformation();

        self::assertStringNotContainsString('href=', $html);
        self::assertStringContainsString('>cv.pdf</span>', $html);
        self::assertStringContainsString('value="' . $fileName . '"', $html);
        self::assertArrayNotHasKey('filepath', $data);
        self::assertSame(['cv.pdf'], $data['pendingfiles']);
    }

    #[Test]
    public function expiredTemporaryFilesAreRemoved(): void
    {
        $element = new Upload('tx_test[file]', 'file', ['directory' => 'fileadmin/test'], $this->createFormStub());
        $old = $element->getTemporaryDirectory() . $element->createTemporaryFileName('old.pdf');
        $recent = $element->getTemporaryDirectory() . $element->createTemporaryFileName('recent.pdf');
        file_put_contents($old, 'x');
        file_put_contents($recent, 'x');
        touch($old, time() - Upload::TEMPORARY_FILE_LIFETIME - 10);
        $this->testFilesToDelete[] = $recent;

        $element->removeExpiredTemporaryFiles();

        self::assertFileDoesNotExist($old);
        self::assertFileExists($recent);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function sanitizeDataProvider(): iterable
    {
        yield 'plain' => ['cv.pdf', 'cv.pdf'];
        yield 'accents are kept' => ['Résumé été.pdf', 'Résumé_été.pdf'];
        yield 'directory traversal' => ['../../etc/passwd', 'passwd'];
        yield 'windows path' => ['C:\\Users\\me\\cv.pdf', 'cv.pdf'];
        yield 'hidden file' => ['.htaccess', 'htaccess'];
        yield 'special characters' => ['a<b>"c";d.txt', 'a_b_c_d.txt'];
        yield 'double extension dots' => ['file..php..txt', 'file.php.txt'];
        yield 'only dots' => ['...', ''];
        yield 'empty' => ['', ''];
    }

    #[Test]
    #[DataProvider('sanitizeDataProvider')]
    public function clientFileNamesAreSanitized(string $fileName, string $expected): void
    {
        self::assertSame($expected, FileNameUtility::sanitize($fileName));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalidFileNameDataProvider(): iterable
    {
        yield 'empty' => [''];
        yield 'dot' => ['.'];
        yield 'parent' => ['..'];
        yield 'traversal' => ['../index.php'];
        yield 'windows traversal' => ['..\\index.php'];
        yield 'sub directory' => ['dir/file.txt'];
    }

    #[Test]
    #[DataProvider('invalidFileNameDataProvider')]
    public function availableNameRequiresAPlainFileName(string $fileName): void
    {
        $this->expectException(\InvalidArgumentException::class);
        FileNameUtility::getAvailableName(sys_get_temp_dir() . '/', $fileName);
    }
}
