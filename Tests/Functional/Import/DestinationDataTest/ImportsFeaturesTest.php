<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Tests\Functional\Import\DestinationDataTest;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;

#[TestDox('DestinationData import')]
class ImportsFeaturesTest extends AbstractTestCase
{
    #[Test]
    public function addsNewFeatures(): void
    {
        $this->setUpConfiguration([
            'restUrl = https://example.com/some-path/',
        ]);
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/FeaturesImportConfiguration.php');
        $this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithFeatures.json') ?: ''),
        ]);
        $tester = $this->executeCommand();

        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportsFeaturesAddsNewFeatures.php');
        $this->assertEmptyLog();
    }

    #[Test]
    public function addsNewFeaturesToExistingOnes(): void
    {
        $this->setUpConfiguration([
            'restUrl = https://example.com/some-path/',
        ]);
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/FeaturesImportConfiguration.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/ExistingFeatures.php');
        $this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithFeatures.json') ?: ''),
        ]);
        $tester = $this->executeCommand();

        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportsFeaturesAddsNewFeatures.php');
        $this->assertEmptyLog();
    }
}
