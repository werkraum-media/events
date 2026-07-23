<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use WerkraumMedia\Events\Domain\Model\Category;
use WerkraumMedia\Events\Domain\Model\Dto\EventDemandFactory;
use WerkraumMedia\Events\Domain\Model\Event;
use WerkraumMedia\Events\Domain\Repository\CategoryRepository;
use WerkraumMedia\Events\Domain\Repository\EventRepository;
use WerkraumMedia\Events\Frontend\MetaInformation\EventMetaInformationInterface;
use WerkraumMedia\Events\Pagination\Factory;
use WerkraumMedia\Events\Service\DataProcessingForModels;

final class EventController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly DataProcessingForModels $dataProcessing,
        private readonly EventDemandFactory $demandFactory,
        private readonly EventMetaInformationInterface $metaInformationService,
        private readonly Factory $paginationFactory,
        private readonly CategoryRepository $categoryRepository
    ) {
    }

    protected function initializeAction(): void
    {
        parent::initializeAction();

        $this->handlePostRequests();
        $this->dataProcessing->setConfigurationManager($this->configurationManager);
    }

    /**
     * @param array<mixed> $search
     */
    public function listAction(array $search = [], int $currentPage = 1): ResponseInterface
    {
        $demand = $this->demandFactory->createFromRequestValues($search, $this->settings);

        $events = $this->eventRepository->findByDemand($demand);

        // Editor-curated selection returns a manually-sorted array; it is a fixed
        // list, not paginated.
        $pagination = null;
        if ($events instanceof QueryResultInterface) {
            $pagination = $this->paginationFactory->create(
                $currentPage,
                (int)($this->settings['itemsPerPage'] ?? 25),
                (int)($this->settings['maximumLinks'] ?? 5),
                $events
            );
        }

        $this->view->assignMultiple([
            'events' => $events,
            'pagination' => $pagination,
            'total' => is_array($events) ? count($events) : $events->count(),
            'demand' => $demand,
            'showSearch' => (bool)($this->settings['showSearch'] ?? false),
            'categoryOptions' => $this->buildCategoryOptions(),
            'userCategoryMap' => array_fill_keys($demand->getUserCategories(), true),
        ]);
        return $this->htmlResponse();
    }

    /**
     * @return array<int, array{parent: Category, children: Category[]}>
     */
    private function buildCategoryOptions(): array
    {
        $editorCategories = GeneralUtility::intExplode(',', (string)($this->settings['categories'] ?? ''), true);

        $options = [];
        foreach ($editorCategories as $parentUid) {
            $parent = $this->categoryRepository->findByUid($parentUid);
            if ($parent instanceof Category === false) {
                continue;
            }
            $options[] = [
                'parent' => $parent,
                'children' => $this->categoryRepository->findAllCurrentlyAssigned($parentUid, 'categories'),
            ];
        }

        return $options;
    }

    #[Extbase\IgnoreValidation(['value' => 'event'])]
    public function showAction(Event $event): ResponseInterface
    {
        $this->metaInformationService->setEvent($event);
        $this->view->assign('event', $event);
        return $this->htmlResponse();
    }

    /**
     * @deprecated Use listAction instead and configure settings properly.
     *             Use Settings or something else to switch between list and teaser rendering.
     */
    public function teaserAction(): ResponseInterface
    {
        $this->view->assignMultiple([
            'events' => $this->eventRepository->findByUids($this->settings['eventUids']),
        ]);
        return $this->htmlResponse();
    }

    public function searchAction(string $search = ''): ResponseInterface
    {
        $this->view->assign('search', $search);
        $this->view->assign('events', $this->eventRepository->findSearchWord($search));
        return $this->htmlResponse();
    }
}
