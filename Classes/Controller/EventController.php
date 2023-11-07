<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Annotation as Extbase;
use WerkraumMedia\Events\Domain\Model\Dto\EventDemandFactory;
use WerkraumMedia\Events\Domain\Model\Event;
use WerkraumMedia\Events\Domain\Repository\EventRepository;
use WerkraumMedia\Events\Service\DataProcessingForModels;

final class EventController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly DataProcessingForModels $dataProcessing,
        private readonly EventDemandFactory $demandFactory
    ) {
    }

    protected function initializeAction(): void
    {
        parent::initializeAction();

        $this->dataProcessing->setConfigurationManager($this->configurationManager);
    }

    public function listAction(): ResponseInterface
    {
        $demand = $this->demandFactory->fromSettings($this->settings);
        $events = $this->eventRepository->findByDemand($demand);
        $this->view->assign('events', $events);
        return $this->htmlResponse();
    }

    #[Extbase\IgnoreValidation(['value' => 'event'])]
    public function showAction(Event $event): ResponseInterface
    {
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
