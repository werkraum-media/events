<?php
namespace Wrm\Events\Controller;

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
        $demand = $this->createDemandFromSettings();
        $dates = $this->dateRepository->findByDemand($demand);
        $this->view->assign('dates', $dates);
    }

    /**
     * @return void
     */
    public function searchAction()
    {

        $searchword = null;
        $regions = null;
        $selRegion = null;
        $dates = null;
        $start = null;
        $end = null;

        if ($this->request->hasArgument('searchword') && $this->request->getArgument('searchword') != '') {
            $searchword = $this->request->getArgument('searchword');
        }

        if ($this->request->hasArgument('region') && $this->request->getArgument('region') != '') {
            $selRegion = $this->request->getArgument('region');
        }

        if ($this->request->hasArgument('start') && $this->request->getArgument('start') != '') {
            $start = date( "Y-m-d", strtotime( $this->request->getArgument('start')));
        }

        if ($this->request->hasArgument('end') && $this->request->getArgument('end') != '') {
            $end = date( "Y-m-d", strtotime( $this->request->getArgument('end')));
        }

        $demand = $this->createDemandFromSearch();
        $dates = $this->dateRepository->findByDemand($demand);

        $regions = $this->regionRepository->findAll();

        $this->view->assign('searchword', $searchword);
        $this->view->assign('regions', $regions);
        $this->view->assign('selRegion', $selRegion);
        $this->view->assign('dates', $dates);
        $this->view->assign('start', $start);
        $this->view->assign('end', $end);

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

        if ($this->request->hasArgument('start') && $this->request->getArgument('start') != '') {
            $demand->setStart(date( "Y-m-d", strtotime( $this->request->getArgument('start'))));
        }

        if ($this->request->hasArgument('end') && $this->request->getArgument('end') != '') {
            $demand->setEnd(date( "Y-m-d", strtotime( $this->request->getArgument('end'))));
        }

        $demand->setSortBy((string)$this->settings['sortByDate']);
        $demand->setSortOrder((string)$this->settings['sortOrder']);

        if (!empty($this->settings['limit'])) {
            $demand->setLimit($this->settings['limit']);
        }

        return $demand;
    }
}
