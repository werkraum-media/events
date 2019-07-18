<?php
namespace Wrm\Events\Controller;

use Wrm\Events\Domain\Model\Dto\EventDemand;
use Wrm\Events\Domain\Model\Event;
use Wrm\Events\Domain\Repository\EventRepository;
use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * EventController
 */

class EventController extends ActionController
{

    /**
     * @var eventRepository
     */
    protected $eventRepository = null;

    /**
     * @var QueryGenerator
     */
    protected $queryGenerator;

    /**
     * @var array
     */
    protected $pluginSettings;

    /**
     * @param EventRepository $eventRepository
     */
    public function injectEventRepository(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
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
     * Action list
     * 
     * @return void
     */
    public function listAction()
    {

        $demand = $this->createDemandFromSettings();
        $events = $this->eventRepository->findByDemand($demand);
        $this->view->assign('events', $events);

    }

    /**
     * Action show
     * 
     * @param Event $event
     * @return void
     */
    public function showAction(Event $event)
    {
        $this->view->assign('event', $event);
    }

    /**
     * action teaser
     * 
     * @return void
     */
    public function teaserAction()
    {
        $events = $this->eventRepository->findByUids($this->settings['eventUids']);
        $this->view->assign('events', $events);
    }

    /**
     * @return EventDemand
     */

    protected function createDemandFromSettings(): EventDemand
    {
        $demand = $this->objectManager->get(EventDemand::class);

        $demand->setRegion((string)$this->settings['region']);

        $demand->setCategories((string)$this->settings['categories']);
        $categoryCombination = (int)$this->settings['categoryCombination'] === 1 ? 'or' : 'and';

        $demand->setCategoryCombination($categoryCombination);

        $demand->setIncludeSubCategories((bool)$this->settings['includeSubcategories']);

        $demand->setSortBy((string)$this->settings['sortByEvent']);
        $demand->setSortOrder((string)$this->settings['sortOrder']);

        $demand->setHighlight((bool)$this->settings['highlight']);

        if (!empty($this->settings['limit'])) {
            $demand->setLimit($this->settings['limit']);
        }

        return $demand;
    }
}
