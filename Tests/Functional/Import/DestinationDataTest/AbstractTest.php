<?php

namespace Wrm\Events\Tests\Functional\Import\DestinationDataTest;

use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

abstract class AbstractTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = [
        'typo3conf/ext/events',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->importDataSet('EXT:events/Tests/Functional/Import/DestinationDataTest/Fixtures/Structure.xml');
        $this->setUpBackendUserFromFixture(1);

        $languageServiceFactory = $this->getContainer()->get(LanguageServiceFactory::class);
        if (!$languageServiceFactory instanceof LanguageServiceFactory) {
            throw new \UnexpectedValueException('Did not retrieve LanguageServiceFactory.', 1637847250);
        }
        $GLOBALS['LANG'] = $languageServiceFactory->create('default');
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['LANG']);

        parent::tearDown();
    }
}
