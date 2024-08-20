<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Tests\Functional\Import\DestinationDataTest;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;

#[TestDox('DestinationData import')]
class ImportsTextsTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpConfiguration([
            'restUrl = https://example.com/some-path/',
            'license = example-license',
            'restType = Event',
            'restLimit = 3',
            'restMode = next_months,12',
            'restTemplate = ET2014A.json',
        ]);
    }

    protected function tearDown(): void
    {
        $this->assertEmptyLog();

        parent::tearDown();
    }

    #[Test]
    public function importsPlainTextIfConfigured(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/DefaultImportConfiguration.php');

        $requests = &$this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithPlainTextTexts.json') ?: ''),
        ]);

        $tester = $this->executeCommand();

        self::assertSame(0, $tester->getStatusCode());
        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportsPlainTextIfConfigured.php');
    }

    #[Test]
    public function importsHtmlTextIfConfiguredAndAvailable(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/ImportConfigurationWithHtml.php');
        $requests = &$this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithHtmlTextTexts.json') ?: ''),
        ]);

        $tester = $this->executeCommand();

        self::assertSame(0, $tester->getStatusCode());
        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportsHtmlTextIfConfigured.php');
    }

    #[Test]
    #[DataProvider('responseWithoutHtml')]
    public function importsPlainTextIfHtmlIsConfiguredButUnavailable(
        string $responseName
    ): void {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/ImportConfigurationWithHtml.php');
        $requests = &$this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithoutHtmlTextTextsDueTo' . $responseName . '.json') ?: ''),
        ]);

        $tester = $this->executeCommand();

        self::assertSame(0, $tester->getStatusCode());
        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportsPlainTextIfHtmlIsConfiguredButUnavailable.php');
    }

    public static function responseWithoutHtml(): iterable
    {
        yield 'Missing type text/html' => [
            'responseName' => 'MissingType',
        ];
        yield 'Empty type text/html' => [
            'responseName' => 'EmptyType',
        ];
    }
}
