<?php

namespace Wrm\Events\Tests\Functional\Import\DestinationDataTest;

use GuzzleHttp\Psr7\Response;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\DateTimeAspect;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ImportDoesntEndUpInEndlessDateCreationTest extends AbstractTest
{
    /**
     * @test
     */
    public function importsExampleAsExpected(): void
    {
        $fileImportPathConfiguration = 'staedte/beispielstadt/events/';
        $fileImportPath = $this->getInstancePath() . '/fileadmin/' . $fileImportPathConfiguration;
        GeneralUtility::mkdir_deep($fileImportPath);

        $this->setDateAspect(new \DateTimeImmutable('2022-07-01'));
        $this->importDataSet('EXT:events/Tests/Functional/Import/DestinationDataTest/Fixtures/SingleRegion.xml');
        $this->importDataSet('EXT:events/Tests/Functional/Import/DestinationDataTest/Fixtures/SingleCategory.xml');
        $this->setUpConfiguration([
            'restUrl = https://example.com/some-path/',
            'license = example-license',
            'restType = Event',
            'restLimit = 3',
            'restMode = next_months,12',
            'restTemplate = ET2014A.json',
            'categoriesPid = 2',
            'categoryParentUid = 2',
        ]);

        $requests = &$this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithPotentiellyEndlessDateCreation.json') ?: ''),
        ]);

        $tester = $this->executeCommand([
            'storage-pid' => '2',
            'rest-experience' => 'beispielstadt',
            'files-folder' => $fileImportPathConfiguration,
            'region-uid' => '1',
        ]);

        self::assertSame(0, $tester->getStatusCode());

        $this->assertCSVDataSet('EXT:events/Tests/Functional/Import/DestinationDataTest/Assertions/ImportDoesntEndUpInEndlessDateCreationTest.csv');
        self::assertFileEquals(
            __DIR__ . '/Assertions/EmptyLogFile.txt',
            $this->getInstancePath() . '/typo3temp/var/log/typo3_0493d91d8e.log',
            'Logfile was not empty.'
        );
    }

    private function setDateAspect(\DateTimeImmutable $dateTime): void
    {
        $context = $this->getContainer()->get(Context::class);
        if (!$context instanceof Context) {
            throw new \TypeError('Retrieved context was of unexpected type.', 1638182021);
        }

        $aspect = new DateTimeAspect($dateTime);
        $context->setAspect('date', $aspect);
    }
}
