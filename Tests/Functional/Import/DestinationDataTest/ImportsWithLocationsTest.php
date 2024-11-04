<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Tests\Functional\Import\DestinationDataTest;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;

#[TestDox('DestinationData import')]
final class ImportsWithLocationsTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/DefaultImportConfiguration.php');
    }

    #[Test]
    public function importsWithLocations(): void
    {
        $this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithLocations.json') ?: ''),
        ]);
        $tester = $this->executeCommand();

        self::assertSame(0, $tester->getStatusCode());
        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportsWithLocations.php');
        $this->assertEmptyLog();
    }

    /**
     * A single location might be available with different ways to write latitude an longitude ("," and ".").
     * This test ensures this situation is properly handled by streamlining the values.
     */
    #[Test]
    public function importsWithSingleLocationOnDuplicates(): void
    {
        $this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithLocationUsingDifferentLatitudeAndLongitude.json') ?: ''),
        ]);
        $tester = $this->executeCommand();

        self::assertSame(0, $tester->getStatusCode());
        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportsWithSingleLocationOnDuplicates.php');
        $this->assertEmptyLog();
    }

    #[Test]
    public function updatesExistingLocation(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/ExistingLocation.php');

        $this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithLocationWithDifferentValues.json') ?: ''),
        ]);
        $tester = $this->executeCommand();

        self::assertSame(0, $tester->getStatusCode());
        $this->assertPHPDataSet(__DIR__ . '/Assertions/UpdatesExistingLocation.php');
        $this->assertEmptyLog();
    }
}
