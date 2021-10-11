<?php

namespace Wrm\Events\Controller;

use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use Wrm\Events\Domain\Model\Date;
use Wrm\Events\Domain\Model\Dto\DateDemand;
use Wrm\Events\Domain\Repository\CategoryRepository;
use Wrm\Events\Domain\Repository\DateRepository;
use Wrm\Events\Domain\Repository\RegionRepository;
use Wrm\Events\Service\DataProcessingForModels;

/**
 * DateController
 */
class DateController extends AbstractController
{
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
     * @var QueryGenerator
     */
    protected $queryGenerator;

    /**
     * @var DataProcessingForModels
     */
    protected $dataProcessing;

    /**
     * @var array
     */
    protected $pluginSettings;

    /*
     * @param RegionRepository $regionRepository
     * @param DateRepository $dateRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        RegionRepository $regionRepository,
        DateRepository $dateRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->regionRepository = $regionRepository;
        $this->dateRepository = $dateRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param DataProcessingForModels $dataProcessing
     */
    public function injectDataProcessingForModels(DataProcessingForModels $dataProcessing): void
    {
        $this->dataProcessing = $dataProcessing;
    }

    protected function initializeAction(): void
    {
        $this->dataProcessing->setConfigurationManager($this->configurationManager);
        $this->pluginSettings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
        );
    }

    public function listAction(): void
    {
        if (
            ($this->request->hasArgument('searchword') && $this->request->getArgument('searchword') != '')
            || ($this->request->hasArgument('region') && $this->request->getArgument('region') != '')
            || ($this->request->hasArgument('start') && $this->request->getArgument('start') != '')
            || ($this->request->hasArgument('end') && $this->request->getArgument('end') != '')
            || ($this->request->hasArgument('events_search') && $this->request->getArgument('events_search') != [])
        ) {
            $demand = $this->createDemandFromSearch();
        } else {
            $demand = $this->createDemandFromSettings();
        }
        $this->view->assign('dates', $this->dateRepository->findByDemand($demand));
    }

    public function searchAction(): void
    {
        $arguments = GeneralUtility::_GET('tx_events_datelist') ?? [];
        if (isset($arguments['events_search'])) {
            $arguments += $arguments['events_search'];
            unset($arguments['events_search']);
        }

        $this->view->assignMultiple([
            'searchword' => $arguments['searchword'] ?? '',
            'selRegion' => $arguments['region'] ?? '',
            'start' => $arguments['start'] ?? '',
            'end' => $arguments['end'] ?? '',
            'considerDate' => $arguments['considerDate'] ?? '',
            'demand' => DateDemand::createFromRequestValues($arguments, $this->settings),
            'regions' => $this->regionRepository->findAll(),
            'categories' => $this->categoryRepository->findAllCurrentlyAssigned(),
        ]);
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

    protected function createDemandFromSettings(): DateDemand
    {
        $demand = $this->objectManager->get(DateDemand::class);

        $demand->setRegion((string)$this->settings['region']);
        $demand->setCategories((string)$this->settings['categories']);
        $categoryCombination = (int)$this->settings['categoryCombination'] === 1 ? 'or' : 'and';
        $demand->setCategoryCombination($categoryCombination);
        $demand->setIncludeSubCategories((bool)$this->settings['includeSubcategories']);
        $demand->setSortBy((string)$this->settings['sortByDate']);
        $demand->setSortOrder((string)$this->settings['sortOrder']);
        $demand->setHighlight((bool)$this->settings['highlight']);
        if (!empty($this->settings['start'])) {
            $demand->setStart((int)$this->settings['start']);
        }
        if (!empty($this->settings['end'])) {
            $demand->setEnd((int)$this->settings['end']);
        }

        if (!empty($this->settings['limit'])) {
            $demand->setLimit($this->settings['limit']);
        }

        return $demand;
    }

    protected function createDemandFromSearch(): DateDemand
    {
        $arguments = $this->request->getArguments() ?? [];
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
