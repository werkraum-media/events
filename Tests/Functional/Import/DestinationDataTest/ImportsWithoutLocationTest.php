<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Tests\Functional\Import\DestinationDataTest;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;

#[TestDox('DestinationData import')]
class ImportsWithoutLocationTest extends AbstractTestCase
{
    #[Test]
    public function importsWithoutLocationIfNotProvided(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SingleImportConfigurationWithoutRegion.php');
        $this->setUpConfiguration([
            'restUrl = https://example.com/some-path/',
            'license = example-license',
            'restType = Event',
            'restLimit = 3',
            'restMode = next_months,12',
            'restTemplate = ET2014A.json',
        ]);

        $requests = &$this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithoutLocation.json') ?: ''),
        ]);
        $tester = $this->executeCommand();

        self::assertSame(0, $tester->getStatusCode());
        self::assertCount(
            0,
            $this->getAllRecords('tx_events_domain_model_location'),
            'Added unexpected location.'
        );
        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportsWithoutLocationIfNotProvided.php');
        $this->assertEmptyLog();
    }
}
