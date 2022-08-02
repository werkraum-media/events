<?php

namespace Wrm\Events\Controller;

use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use Wrm\Events\Domain\Model\Date;
use Wrm\Events\Domain\Model\Dto\DateDemand;
use Wrm\Events\Domain\Model\Dto\DateDemandFactory;
use Wrm\Events\Domain\Repository\CategoryRepository;
use Wrm\Events\Domain\Repository\DateRepository;
use Wrm\Events\Domain\Repository\RegionRepository;
use Wrm\Events\Events\Controller\DateListVariables;
use Wrm\Events\Events\Controller\DateSearchVariables;
use Wrm\Events\Pagination\Factory;
use Wrm\Events\Service\DataProcessingForModels;

/**
 * DateController
 */
class DateController extends AbstractController
{
    /**
     * @var DateDemandFactory
     */
    protected $demandFactory;

    /**
     * @var dateRepository
     */
    protected $dateRepository;

    /**
     * @var regionRepository
     */
    protected $regionRepository;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var Factory
     */
    protected $paginationFactory;

    /**
     * @var DataProcessingForModels
     */
    protected $dataProcessing;

    public function __construct(
        DateDemandFactory $demandFactory,
        RegionRepository $regionRepository,
        DateRepository $dateRepository,
        CategoryRepository $categoryRepository,
        DataProcessingForModels $dataProcessing,
        EventDispatcher $eventDispatcher,
        Factory $paginationFactory
    ) {
        $this->demandFactory = $demandFactory;
        $this->regionRepository = $regionRepository;
        $this->dateRepository = $dateRepository;
        $this->categoryRepository = $categoryRepository;
        $this->dataProcessing = $dataProcessing;
        $this->eventDispatcher = $eventDispatcher;
        $this->paginationFactory = $paginationFactory;
    }

    protected function initializeAction(): void
    {
        $contentObject = $this->configurationManager->getContentObject();
        if ($contentObject !== null) {
            $this->demandFactory->setContentObjectRenderer($contentObject);
        }
        $this->dataProcessing->setConfigurationManager($this->configurationManager);
    }

    /**
     * @param array $search
     * @param int $currentPage
     */
    public function listAction(
        array $search = [],
        int $currentPage = 1
    ): void {
        $demand = $this->demandFactory->fromSettings($this->settings);
        if ($search !== []) {
            $demand = DateDemand::createFromRequestValues($search, $this->settings);
        } elseif (
            ($this->request->hasArgument('searchword') && $this->request->getArgument('searchword') != '')
            || ($this->request->hasArgument('region') && $this->request->getArgument('region') != '')
            || ($this->request->hasArgument('start') && $this->request->getArgument('start') != '')
            || ($this->request->hasArgument('end') && $this->request->getArgument('end') != '')
            || ($this->request->hasArgument('events_search') && $this->request->getArgument('events_search') != [])
        ) {
            $demand = $this->createDemandFromSearch();
        }

        $dates = $this->dateRepository->findByDemand($demand);
        $event = $this->eventDispatcher->dispatch(new DateListVariables(
            $search,
            $demand,
            $dates,
            $this->paginationFactory->create(
                $currentPage,
                $this->settings['itemsPerPage'] ?? 25,
                $this->settings['maximumLinks'] ?? 5,
                $dates
            )
        ));
        if (!$event instanceof DateListVariables) {
            throw new \Exception('Did not retrieve DateSearchVariables from event dispatcher, got: ' . get_class($event), 1657542318);
        }
        $this->view->assignMultiple($event->getVariablesForView());
    }

    /**
     * @param array $search
     */
    public function searchAction(array $search = []): void
    {
        $arguments = GeneralUtility::_GET('tx_events_datelist') ?? $search;
        if (is_array($arguments) === false) {
            $arguments = [];
        }
        if (isset($arguments['events_search']) && is_array($arguments['events_search'])) {
            $arguments += $arguments['events_search'];
            unset($arguments['events_search']);
        }

        // For legacy systems.
        $this->view->assignMultiple([
            'searchword' => $arguments['searchword'] ?? '',
            'selRegion' => $arguments['region'] ?? '',
            'start' => $arguments['start'] ?? '',
            'end' => $arguments['end'] ?? '',
            'considerDate' => $arguments['considerDate'] ?? '',
        ]);

        $demand = $this->demandFactory->fromSettings($this->settings);
        if ($search !== []) {
            $demand = DateDemand::createFromRequestValues($search, $this->settings);
        }

        $event = $this->eventDispatcher->dispatch(new DateSearchVariables(
            $search,
            $demand,
            $this->regionRepository->findAll(),
            $this->categoryRepository->findAllCurrentlyAssigned($this->settings['uids']['categoriesParent'] ?? 0, 'categories'),
            $this->categoryRepository->findAllCurrentlyAssigned($this->settings['uids']['featuresParent'] ?? 0, 'features')
        ));
        if (!$event instanceof DateSearchVariables) {
            throw new \Exception('Did not retrieve DateSearchVariables from event dispatcher, got: ' . get_class($event), 1657542318);
        }
        $this->view->assignMultiple($event->getVariablesForView());
    }

    public function teaserAction(): void
    {
        $dates = $this->dateRepository->findByUids($this->settings['eventUids']);
        $this->view->assign('dates', $dates);
    }

    /**
     * @Extbase\IgnoreValidation("date")
     */
    public function showAction(Date $date): void
    {
        $this->view->assign('date', $date);
    }

    protected function createDemandFromSearch(): DateDemand
    {
        $arguments = $this->request->getArguments();
        if (isset($arguments['events_search'])) {
            $arguments += $arguments['events_search'];
            unset($arguments['events_search']);
        }

        return DateDemand::createFromRequestValues(
            $arguments,
            $this->settings
        );
    }
}
