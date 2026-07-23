<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Tests\Functional\Event;

use Codappix\Typo3PhpDatasets\TestingFramework;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use WerkraumMedia\Events\Domain\Model\Dto\EventDemand;
use WerkraumMedia\Events\Domain\Model\Event;
use WerkraumMedia\Events\Domain\Repository\EventRepository;

/**
 * Repository-level coverage for the new visitor search: free-text over
 * title/subtitle/teaser/details and OR-combined category filtering, driven
 * through EventDemand (not the legacy title-only findSearchWord).
 *
 * Self-contained: loads only the real extension, no `example` fixture extension.
 */
class EventSearchDemandTest extends FunctionalTestCase
{
    use TestingFramework;

    protected array $coreExtensionsToLoad = [
        'filelist',
        'filemetadata',
        'install',
        'fluid',
        'extbase',
    ];

    protected array $testExtensionsToLoad = [
        'werkraummedia/events',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/Fixtures/EventsForSearch.php');
    }

    /**
     * @return string[]
     */
    protected function findTitlesByDemand(EventDemand $demand): array
    {
        $repository = $this->get(EventRepository::class);

        $titles = [];
        foreach ($repository->findByDemand($demand) as $event) {
            self::assertInstanceOf(Event::class, $event);
            $titles[] = $event->getTitle();
        }

        return $titles;
    }

    #[Test]
    public function searchwordMatchesTitle(): void
    {
        $demand = new EventDemand();
        $demand->setSearchword('Stadtmuseum');

        self::assertSame(['Stadtmuseum Erfurt'], $this->findTitlesByDemand($demand));
    }

    #[Test]
    public function searchwordMatchesSubtitle(): void
    {
        $demand = new EventDemand();
        $demand->setSearchword('Wahrzeichen');

        self::assertSame(['Domberg Erfurt'], $this->findTitlesByDemand($demand));
    }

    #[Test]
    public function searchwordMatchesTeaser(): void
    {
        $demand = new EventDemand();
        $demand->setSearchword('Wohnhaus');

        self::assertSame(['Goethehaus Weimar'], $this->findTitlesByDemand($demand));
    }

    #[Test]
    public function searchwordMatchesDetails(): void
    {
        $demand = new EventDemand();
        $demand->setSearchword('Klassik');

        self::assertSame(['Goethehaus Weimar'], $this->findTitlesByDemand($demand));
    }

    #[Test]
    public function categoriesMatchAnySelectedCategory(): void
    {
        $demand = new EventDemand();
        // Museum (10) → Goethehaus, Kirche (11) → Domberg; OR-combined.
        $demand->setCategories([10, 11]);

        $titles = $this->findTitlesByDemand($demand);
        sort($titles);

        self::assertSame(['Domberg Erfurt', 'Goethehaus Weimar'], $titles);
    }

    #[Test]
    public function searchwordAndCategoryNarrowWithAndLogic(): void
    {
        $demand = new EventDemand();
        $demand->setSearchword('Weimar');
        // Goethehaus is in Museum (10); Domberg (Kirche 11) would fail the searchword.
        $demand->setCategories([10]);

        self::assertSame(['Goethehaus Weimar'], $this->findTitlesByDemand($demand));
    }

    #[Test]
    public function userCategoriesRefineWithinEditorCategories(): void
    {
        $demand = new EventDemand();
        // Editor scope: Museum (10) + Kirche (11) → Goethehaus + Domberg.
        $demand->setCategories([10, 11]);
        // Visitor narrows to Museum (10) → only Goethehaus survives.
        $demand->setUserCategories([10]);

        self::assertSame(['Goethehaus Weimar'], $this->findTitlesByDemand($demand));
    }

    #[Test]
    public function userCategoriesAloneApplyWithoutEditorScope(): void
    {
        $demand = new EventDemand();
        $demand->setUserCategories([11]);

        self::assertSame(['Domberg Erfurt'], $this->findTitlesByDemand($demand));
    }
}
