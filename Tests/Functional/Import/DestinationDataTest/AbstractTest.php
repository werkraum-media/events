<?php

namespace Wrm\Events\Tests\Functional\Import\DestinationDataTest;

use Wrm\Events\Tests\Functional\AbstractFunctionalTestCase;

abstract class AbstractTest extends AbstractFunctionalTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/Structure.php');
    }
}
