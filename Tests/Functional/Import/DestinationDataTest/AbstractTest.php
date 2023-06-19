<?php

namespace Wrm\Events\Tests\Functional\Import\DestinationDataTest;

use Wrm\Events\Tests\Functional\AbstractFunctionalTestCase;

abstract class AbstractTest extends AbstractFunctionalTestCase
{
    protected function setUp(): void
    {
        $this->coreExtensionsToLoad = [
            'filemetadata',
        ];

        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/Structure.php');
    }
}
