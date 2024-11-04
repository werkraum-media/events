<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Tests\Functional\Import\DestinationDataTest;

use DateTimeImmutable;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\Test;

class ImportDoesntEndUpInEndlessDateCreationTest extends AbstractTestCase
{
    #[Test]
    public function importsExampleAsExpected(): void
    {
        $this->setDateAspect(new DateTimeImmutable('2022-07-01'));
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/DefaultImportConfiguration.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SingleRegion.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SingleCategory.php');

        $requests = &$this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithPotentiellyEndlessDateCreation.json') ?: ''),
        ]);

        $tester = $this->executeCommand();

        self::assertSame(0, $tester->getStatusCode());

        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportDoesntEndUpInEndlessDateCreation.php');
        $this->assertEmptyLog();
    }
}
