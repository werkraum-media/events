<?php

namespace Wrm\Events\Controller;

use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use Wrm\Events\Domain\Model\Date;
use Wrm\Events\Domain\Model\Dto\DateDemand;
use Wrm\Events\Domain\Model\Dto\DateDemandFactory;
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
        DateDemandFactory $demandFactory,
        RegionRepository $regionRepository,
        DateRepository $dateRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->demandFactory = $demandFactory;
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
        $contentObject = $this->configurationManager->getContentObject();
        if ($contentObject !== null) {
            $this->demandFactory->setContentObjectRenderer($contentObject);
        }
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
            $demand = $this->demandFactory->fromSettings($this->settings);
        }
        $dates = $this->dateRepository->findByDemand($demand);
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
