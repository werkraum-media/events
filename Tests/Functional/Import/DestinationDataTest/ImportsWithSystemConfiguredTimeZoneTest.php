<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Tests\Functional\Import\DestinationDataTest;

use DateTimeImmutable;
use DateTimeZone;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use TYPO3\CMS\Core\Utility\ArrayUtility;

#[TestDox('DestinationData import')]
class ImportsWithSystemConfiguredTimeZoneTest extends AbstractTestCase
{
    public function setUp(): void
    {
        ArrayUtility::mergeRecursiveWithOverrule($this->configurationToUseInTestInstance, [
            'SYS' => [
                'phpTimeZone' => 'Europe/Berlin',
            ],
        ]);

        parent::setUp();

        $this->setUpConfiguration([
            'restUrl = https://example.com/some-path/',
        ]);
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/DefaultImportConfiguration.php');
        $this->setDateAspect(new DateTimeImmutable('2022-07-13', new DateTimeZone('UTC')));
    }

    #[Test]
    public function withTYPO3TimeZone(): void
    {
        $this->setUpResponses([new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithSingleDate.json') ?: '')]);

        $this->executeCommand();

        $dates = $this->getAllRecords('tx_events_domain_model_date');
        self::assertSame('kurzfuehrung-historische-altstadt-2022-07-13t15-00-00', $dates[0]['slug']);

        $this->assertEmptyLog();
    }
}
