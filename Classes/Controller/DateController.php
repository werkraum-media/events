<?php
namespace Wrm\Events\Controller;


/***
 *
 * This file is part of the "DD Events" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Dirk Koritnik <koritnik@werkraum-media.de>
 *
 ***/

use Wrm\Events\Domain\Model\Dto\DateDemand;
use Wrm\Events\Domain\Model\Date;
use Wrm\Events\Domain\Repository\DateRepository;
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
    protected $dateRepository = null;

    /**
     * @var QueryGenerator
     */
    protected $queryGenerator;

    /**
     * @var array
     */
    protected $pluginSettings;

    /**
     * @param DateRepository $dateRepository
     */
    public function injectDateRepository(DateRepository $dateRepository)
    {
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

        $demand->setHighlight((string)$this->settings['highlight']);

        if (!empty($this->settings['limit'])) {
            $demand->setLimit($this->settings['limit']);
        }

        return $demand;
    }
}
