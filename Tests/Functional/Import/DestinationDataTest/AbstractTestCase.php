<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Tests\Functional\Import\DestinationDataTest;

use WerkraumMedia\Events\Tests\Functional\AbstractFunctionalTestCase;

abstract class AbstractTestCase extends AbstractFunctionalTestCase
{
    protected function setUp(): void
    {
        $this->coreExtensionsToLoad[] = 'filemetadata';

        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/Structure.php');
    }

    protected function tearDown(): void
    {
        // Empty log file for upcoming tests.
        file_put_contents($this->getLogFile(), '');

        parent::tearDown();
    }

    protected function assertEmptyLog(): void
    {
        self::assertFileEquals(
            __DIR__ . '/Assertions/EmptyLogFile.txt',
            $this->getLogFile(),
            'Logfile was not empty.'
        );
    }

    protected function assertMessageInLog(string $message): void
    {
        self::assertStringContainsString(
            $message,
            file_get_contents($this->getLogFile()),
            'Logfile did not contain the message.'
        );
    }

    private function getLogFile(): string
    {
        return $this->getInstancePath() . '/typo3temp/var/log/typo3_0493d91d8e.log';
    }
}
