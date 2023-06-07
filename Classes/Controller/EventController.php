<?php

namespace Wrm\Events\Controller;

use TYPO3\CMS\Extbase\Annotation as Extbase;
use Wrm\Events\Domain\Model\Dto\EventDemandFactory;
use Wrm\Events\Domain\Model\Event;
use Wrm\Events\Domain\Repository\EventRepository;
use Wrm\Events\Service\DataProcessingForModels;

class EventController extends AbstractController
{
    /**
     * @var EventRepository
     */
    protected $eventRepository;

    /**
     * @var DataProcessingForModels
     */
    protected $dataProcessing;

    /**
     * @var EventDemandFactory
     */
    protected $demandFactory;

    public function __construct(
        EventRepository $eventRepository,
        DataProcessingForModels $dataProcessing,
        EventDemandFactory $demandFactory
    ) {
        $this->eventRepository = $eventRepository;
        $this->dataProcessing = $dataProcessing;
        $this->demandFactory = $demandFactory;
    }

    protected function initializeAction(): void
    {
        $this->dataProcessing->setConfigurationManager($this->configurationManager);
    }

    public function listAction(): void
    {
        $demand = $this->demandFactory->fromSettings($this->settings);
        $events = $this->eventRepository->findByDemand($demand);
        $this->view->assign('events', $events);
    }

    /**
     * @Extbase\IgnoreValidation("event")
     */
    public function showAction(Event $event): void
    {
        $this->view->assign('event', $event);
    }

    /**
     * @deprecated Use listAction instead and configure settings properly.
     *             Use Settings or something else to switch between list and teaser rendering.
     */
    public function teaserAction(): void
    {
        $this->view->assignMultiple([
            'events' => $this->eventRepository->findByUids($this->settings['eventUids']),
        ]);
    }

    public function searchAction(string $search = ''): void
    {
        $this->view->assign('search', $search);
        $this->view->assign('events', $this->eventRepository->findSearchWord($search));
    }
}
