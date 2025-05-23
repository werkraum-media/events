<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Tests\Functional\Import\DestinationDataTest;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use TYPO3\CMS\Core\Utility\GeneralUtility;

#[TestDox('DestinationData import')]
class ImportHandlesImagesTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        // Ensure proper type mapping within tests.
        // Do not use system magic as this might be different within CI and somewhere else.
        $this->configurationToUseInTestInstance['SYS']['FileInfo']['fileExtensionToMimeType']['jpg'] = 'image/jpg';

        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/DefaultImportConfiguration.php');
    }

    #[Test]
    public function addsNewImages(): void
    {
        $this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithNewImages.json') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
        ]);

        $this->executeCommand();

        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportHandlesImagesAddsNewImages.php');

        $importedFiles = GeneralUtility::getFilesInDir($this->fileImportPath);
        self::assertIsArray($importedFiles, 'Failed to retrieve imported files from filesystem.');
        self::assertSame(
            [
                'lutherkirche-jpg.jpg',
                'theater-rudolstadt_johannes-gei-er_photo-by-lisa-stern_web_-jpg.jpg',
                'tueftlerzeit-sfz-rudolstadt-jpg.jpg',
            ],
            array_values($importedFiles),
            'Got unexpected number of files'
        );

        $this->assertEmptyLog();
    }

    #[Test]
    public function addsNewImageWithoutFileName(): void
    {
        $this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithNewImageWithoutFileName.json') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
        ]);

        $this->executeCommand();

        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportHandlesImageWithoutFileName.php');

        $importedFiles = GeneralUtility::getFilesInDir($this->fileImportPath);
        self::assertIsArray($importedFiles, 'Failed to retrieve imported files from filesystem.');
        self::assertSame(
            [
                'bf126089c94f95031fa07bf9d7d9b10c3e58aafebdef31f0b60604da13019b8d.jpg',
            ],
            array_values($importedFiles),
            'Got unexpected number of files'
        );

        $this->assertEmptyLog();
    }

    #[Test]
    public function addsMultipleImagesToSingleEvent(): void
    {
        $this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithMultipleImagesForSingleEvent.json') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
        ]);

        $this->executeCommand();

        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportHandlesImagesAddsMultipleImagestoSingleEvent.php');

        $importedFiles = GeneralUtility::getFilesInDir($this->fileImportPath);
        self::assertIsArray($importedFiles, 'Failed to retrieve imported files from filesystem.');
        self::assertSame(
            [
                'theater-rudolstadt_johannes-gei-er_photo-by-lisa-stern_web_-jpg.jpg',
                'tueftlerzeit-sfz-rudolstadt-jpg.jpg',
            ],
            array_values($importedFiles),
            'Got unexpected number of files'
        );

        $this->assertEmptyLog();
    }

    #[Test]
    public function removesNoLongerExistingImages(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/ImportHandlesImagesExistingData.php');
        copy(__DIR__ . '/Fixtures/ExampleImage.jpg', $this->fileImportPath . '/theater-rudolstadt_johannes-gei-er_photo-by-lisa-stern_web_-jpg.jpg');
        copy(__DIR__ . '/Fixtures/ExampleImage.jpg', $this->fileImportPath . '/tueftlerzeit-sfz-rudolstadt-jpg.jpg');

        $this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithSingleImageForSingleEvent.json') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
        ]);

        $this->executeCommand();

        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportHandlesImagesRemovesNoLongerExistingImages.php');

        $importedFiles = GeneralUtility::getFilesInDir($this->fileImportPath);
        self::assertIsArray($importedFiles, 'Failed to retrieve imported files from filesystem.');
        self::assertSame(
            [
                'theater-rudolstadt_johannes-gei-er_photo-by-lisa-stern_web_-jpg.jpg',
                'tueftlerzeit-sfz-rudolstadt-jpg.jpg',
            ],
            array_values($importedFiles),
            'Got unexpected number of files'
        );

        $this->assertEmptyLog();
    }

    #[Test]
    public function updatesExistingImage(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/ImportHandlesImagesExistingData.php');
        copy(__DIR__ . '/Fixtures/ExampleImage.jpg', $this->fileImportPath . '/theater-rudolstadt_johannes-gei-er_photo-by-lisa-stern_web_-jpg.jpg');
        copy(__DIR__ . '/Fixtures/ExampleImage.jpg', $this->fileImportPath . '/tueftlerzeit-sfz-rudolstadt-jpg.jpg');

        $this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithMultipleImagesForSingleEvent.json') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
        ]);

        $this->executeCommand();

        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportHandlesImagesUpdatesExistingImage.php');

        $importedFiles = GeneralUtility::getFilesInDir($this->fileImportPath);
        self::assertIsArray($importedFiles, 'Failed to retrieve imported files from filesystem.');
        self::assertSame(
            [
                'theater-rudolstadt_johannes-gei-er_photo-by-lisa-stern_web_-jpg.jpg',
                'tueftlerzeit-sfz-rudolstadt-jpg.jpg',
            ],
            array_values($importedFiles),
            'Got unexpected number of files'
        );

        $this->assertEmptyLog();
    }

    #[Test]
    public function addsNewImageToExistingImages(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/ImportHandlesImagesExistingData.php');
        copy(__DIR__ . '/Fixtures/ExampleImage.jpg', $this->fileImportPath . '/theater-rudolstadt_johannes-gei-er_photo-by-lisa-stern_web_-jpg.jpg');
        copy(__DIR__ . '/Fixtures/ExampleImage.jpg', $this->fileImportPath . '/tueftlerzeit-sfz-rudolstadt-jpg.jpg');

        $this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithOneMoreImageForSingleEvent.json') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
        ]);

        $this->executeCommand();

        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportHandlesImagesAddsNewImageToExistingImages.php');

        $importedFiles = GeneralUtility::getFilesInDir($this->fileImportPath);
        self::assertIsArray($importedFiles, 'Failed to retrieve imported files from filesystem.');
        self::assertSame(
            [
                'lutherkirche-jpg.jpg',
                'theater-rudolstadt_johannes-gei-er_photo-by-lisa-stern_web_-jpg.jpg',
                'tueftlerzeit-sfz-rudolstadt-jpg.jpg',
            ],
            array_values($importedFiles),
            'Got unexpected number of files'
        );

        $this->assertEmptyLog();
    }

    #[Test]
    public function updatesSortingOfImages(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/ImportHandlesImagesExistingData.php');
        copy(__DIR__ . '/Fixtures/ExampleImage.jpg', $this->fileImportPath . '/theater-rudolstadt_johannes-gei-er_photo-by-lisa-stern_web_-jpg.jpg');
        copy(__DIR__ . '/Fixtures/ExampleImage.jpg', $this->fileImportPath . '/tueftlerzeit-sfz-rudolstadt-jpg.jpg');

        $this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithSortedImagesForSingleEvent.json') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
        ]);

        $this->executeCommand();

        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportHandlesImagesUpdatesSortingOfImages.php');

        $importedFiles = GeneralUtility::getFilesInDir($this->fileImportPath);
        self::assertIsArray($importedFiles, 'Failed to retrieve imported files from filesystem.');
        self::assertSame(
            [
                'theater-rudolstadt_johannes-gei-er_photo-by-lisa-stern_web_-jpg.jpg',
                'tueftlerzeit-sfz-rudolstadt-jpg.jpg',
            ],
            array_values($importedFiles),
            'Got unexpected number of files'
        );

        $this->assertEmptyLog();
    }
}
