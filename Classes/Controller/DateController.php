<?php
namespace Wrm\Events\Controller;

use Wrm\Events\Domain\Model\Date;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Wrm\Events\Domain\Model\Dto\DateDemand;
use Wrm\Events\Domain\Repository\CategoryRepository;
use Wrm\Events\Domain\Repository\DateRepository;
use Wrm\Events\Domain\Repository\RegionRepository;
use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * DateController
 */
class DateController extends ActionController
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
     * Action initializer
     */
    protected function initializeAction()
    {
        $this->pluginSettings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
        );
    }

    /**
     * action list
     *
     * @return void
     */
    public function listAction()
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

    /**
     * @return void
     */
    public function searchAction()
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

    /**
     * action teaser
     *
     * @return void
     */
    public function teaserAction()
    {
        $dates = $this->dateRepository->findByUids($this->settings['eventUids']);
        $this->view->assign('dates', $dates);
    }

    /**
     * action show
     *
     * @param \Wrm\Events\Domain\Model\Date $date
     * @return void
     */
    public function showAction(Date $date)
    {
        $this->view->assign('date', $date);
    }

    /**
     * @return DateDemand
     */
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
        $demand->setHighlight((int)$this->settings['highlight']);

        if (!empty($this->settings['limit'])) {
            $demand->setLimit($this->settings['limit']);
        }

        return $demand;
    }

    /**
     * @return DateDemand
     */
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
