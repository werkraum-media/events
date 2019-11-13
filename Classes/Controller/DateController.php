<?php
namespace Wrm\Events\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Wrm\Events\Domain\Model\Dto\DateDemand;
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
     */
    public function __construct(
        RegionRepository $regionRepository,
        DateRepository $dateRepository
    ) {
        $this->regionRepository = $regionRepository;
        $this->dateRepository = $dateRepository;
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
        if (($this->request->hasArgument('searchword') && $this->request->getArgument('searchword') != '') ||
            ($this->request->hasArgument('region') && $this->request->getArgument('region') != '') ||
            ($this->request->hasArgument('start') && $this->request->getArgument('start') != '') ||
            ($this->request->hasArgument('end') && $this->request->getArgument('end') != ''))
        {
            $demand = $this->createDemandFromSearch();
            $dates = $this->dateRepository->findByDemand($demand);
        } else {
            $demand = $this->createDemandFromSettings();
            $dates = $this->dateRepository->findByDemand($demand);
        }
        $this->view->assign('dates', $dates);
    }

    /**
     * @return void
     */
    public function searchAction()
    {
        $arguments = GeneralUtility::_GET('tx_events_datelist');
        $searchword = $arguments['searchword'];
        $selRegion = $arguments['region'];
        $start = $arguments['start'];
        $end = $arguments['end'];
        $considerDate = $arguments['considerDate'];

        $regions = $this->regionRepository->findAll();
        $this->view->assign('regions', $regions);

        $this->view->assign('searchword', $searchword);
        $this->view->assign('selRegion', $selRegion);
        $this->view->assign('start', $start);
        $this->view->assign('end', $end);
        $this->view->assign('considerDate', $considerDate);
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
    public function showAction(\Wrm\Events\Domain\Model\Date $date)
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
        $demand = $this->objectManager->get(DateDemand::class);

        if ($this->request->hasArgument('region') && $this->request->getArgument('region') != '')
            $demand->setRegion((string)$this->request->getArgument('region'));

        if ($this->request->hasArgument('highlight') && $this->request->hasArgument('highlight') != '')
            $demand->setHighlight((int)$this->settings['highlight']);

        if ($this->request->hasArgument('searchword') && $this->request->getArgument('searchword') != '')
            $demand->setSearchword((string)$this->request->getArgument('searchword'));

        if ($this->request->hasArgument('start') && $this->request->getArgument('start') != '')
            $demand->setStart(strtotime($this->request->getArgument('start')));

        if ($this->request->hasArgument('end') && $this->request->getArgument('end') != '')
            $demand->setEnd(strtotime($this->request->getArgument('end')));

        if ($this->request->hasArgument('considerDate') && $this->request->getArgument('considerDate') != '')
            $demand->setConsiderDate(strtotime($this->request->getArgument('considerDate')));

        $demand->setSortBy((string)$this->settings['sortByDate']);
        $demand->setSortOrder((string)$this->settings['sortOrder']);

        if (!empty($this->settings['limit'])) {
            $demand->setLimit($this->settings['limit']);
        }

        return $demand;
    }
}
