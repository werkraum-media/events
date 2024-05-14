<?php

namespace Wrm\Events\Tests\Functional\Import\DestinationDataTest;

use Codappix\Typo3PhpDatasets\PhpDataSet;
use GuzzleHttp\Psr7\Response;

/**
 * @testdox DestinationData import
 */
class ImportHandlesPricesTest extends AbstractTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/DefaultImportConfiguration.php');
        $this->setUpConfiguration([
            'restUrl = https://example.com/some-path/',
            'license = example-license',
            'restType = Event',
            'restLimit = 3',
            'restMode = next_months,12',
            'restTemplate = ET2014A.json',
        ]);
    }

    /**
     * @test
     */
    public function addsNewPriceFromPriceInfo(): void
    {
        $this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithNewPriceInfo.json') ?: ''),
        ]);

        $this->executeCommand();

        self::assertSame(
            'Preis Info',
            $this->getAllRecords('tx_events_domain_model_event')[0]['price_info'] ?? ''
        );

        $this->assertEmptyLog();
    }

    /**
     * @test
     */
    public function addsNewPriceFromPriceInfoExtra(): void
    {
        $this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithNewPriceInfoExtra.json') ?: ''),
        ]);

        $this->executeCommand();

        self::assertSame(
            'Preis Info Extra',
            $this->getAllRecords('tx_events_domain_model_event')[0]['price_info'] ?? ''
        );

        $this->assertEmptyLog();
    }

    /**
     * @test
     */
    public function keepsNoLongerExistingPrice(): void
    {
        (new PhpDataSet())->import(['tx_events_domain_model_event' => [0 => [
            'uid' => 1,
            'pid' => 2,
            'global_id' => 'e_100350503',
            'price_info' => 'Existing price info',
        ]]]);

        $this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithNoPriceInfo.json') ?: ''),
        ]);

        $this->executeCommand();

        self::assertSame(
            'Existing price info',
            $this->getAllRecords('tx_events_domain_model_event')[0]['price_info'] ?? ''
        );

        $this->assertEmptyLog();
    }

    /**
     * @test
     */
    public function updatesExistingPriceFromPriceInfo(): void
    {
        (new PhpDataSet())->import(['tx_events_domain_model_event' => [0 => [
            'uid' => 1,
            'pid' => 2,
            'global_id' => 'e_100350503',
            'price_info' => 'Existing price info',
        ]]]);

        $this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithNewPriceInfo.json') ?: ''),
        ]);

        $this->executeCommand();

        self::assertSame(
            'Preis Info',
            $this->getAllRecords('tx_events_domain_model_event')[0]['price_info'] ?? ''
        );

        $this->assertEmptyLog();
    }

    /**
     * @test
     */
    public function updatesExistingPriceFromPriceInfoExtra(): void
    {
        (new PhpDataSet())->import(['tx_events_domain_model_event' => [0 => [
            'uid' => 1,
            'pid' => 2,
            'global_id' => 'e_100350503',
            'price_info' => 'Existing price info',
        ]]]);

        $this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithNewPriceInfoExtra.json') ?: ''),
        ]);

        $this->executeCommand();

        self::assertSame(
            'Preis Info Extra',
            $this->getAllRecords('tx_events_domain_model_event')[0]['price_info'] ?? ''
        );

        $this->assertEmptyLog();
    }
}
