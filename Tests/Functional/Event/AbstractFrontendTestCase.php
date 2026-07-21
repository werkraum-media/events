<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Tests\Functional\Event;

use Codappix\Typo3PhpDatasets\TestingFramework;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Self-contained frontend harness for the Event plugins (list/search).
 * Deliberately does NOT use the shared `example` fixture extension — it loads
 * the real extensions and ships its own Sites fixture + rendering TypoScript,
 * mirroring EXT:thuecat's TouristAttraction suite.
 */
abstract class AbstractFrontendTestCase extends FunctionalTestCase
{
    use TestingFramework;

    protected function setUp(): void
    {
        $this->coreExtensionsToLoad = [
            'core',
            'backend',
            'extbase',
            'fluid',
            'frontend',
            'install',
        ];

        $this->testExtensionsToLoad = [
            'werkraummedia/events',
        ];

        $this->pathsToLinkInTestInstance = [
            'typo3conf/ext/events/Tests/Functional/Event/Fixtures/Sites/' => 'typo3conf/sites',
        ];

        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/Fixtures/' . $this->getDataSetFileName());
        $this->setUpFrontendRootPage(1, [
            'EXT:events/Configuration/TypoScript/setup.typoscript',
            'EXT:events/Tests/Functional/Event/Fixtures/' . $this->getRenderingTypoScript(),
        ]);
    }

    /** PHP data-set filename under Fixtures/. */
    abstract protected function getDataSetFileName(): string;

    /** Rendering TypoScript filename under Fixtures/. */
    protected function getRenderingTypoScript(): string
    {
        return 'ListRendering.typoscript';
    }
}
