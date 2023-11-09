<?php

namespace WerkraumMedia\Events\Tests\Functional\Import\DestinationDataTest;

use WerkraumMedia\Events\Tests\Functional\AbstractFunctionalTestCase;

abstract class AbstractTest extends AbstractFunctionalTestCase
{
    protected function setUp(): void
    {
        $this->coreExtensionsToLoad[] = 'filemetadata';

        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/Structure.php');
    }

    protected function assertEmptyLog(): void
    {
        self::assertFileEquals(
            __DIR__ . '/Assertions/EmptyLogFile.txt',
            $this->getInstancePath() . '/typo3temp/var/log/typo3_0493d91d8e.log',
            'Logfile was not empty.'
        );
    }
}
