<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Controller;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\Service\ExtensionService;
use WerkraumMedia\Events\Domain\Model\Date;
use WerkraumMedia\Events\Domain\Model\Dto\DateDemandFactory;
use WerkraumMedia\Events\Domain\Repository\CategoryRepository;
use WerkraumMedia\Events\Domain\Repository\DateRepository;
use WerkraumMedia\Events\Domain\Repository\RegionRepository;
use WerkraumMedia\Events\Events\Controller\DateListVariables;
use WerkraumMedia\Events\Events\Controller\DateSearchVariables;
use WerkraumMedia\Events\Frontend\MetaInformation\DateMetaInformationInterface;
use WerkraumMedia\Events\Pagination\Factory;
use WerkraumMedia\Events\Service\DataProcessingForModels;

final class DateController extends AbstractController
{
    public function __construct(
        private readonly DateDemandFactory $demandFactory,
        private readonly DateRepository $dateRepository,
        private readonly RegionRepository $regionRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly Factory $paginationFactory,
        private readonly DataProcessingForModels $dataProcessing,
        private readonly ExtensionService $extensionService,
        private readonly DateMetaInformationInterface $metaInformationService
    ) {
    }

    protected function initializeAction(): void
    {
        parent::initializeAction();

        $contentObject = $this->request->getAttribute('currentContentObject');
        if ($contentObject !== null) {
            $this->demandFactory->setContentObjectRenderer($contentObject);
        }
        $this->dataProcessing->setConfigurationManager($this->configurationManager);

        $this->handlePostRequest();
    }

    public function listAction(
        array $search = [],
        int $currentPage = 1
    ): ResponseInterface {
        $demand = $this->demandFactory->fromSettings($this->settings);
        if ($search !== []) {
            $demand = $this->demandFactory->createFromRequestValues($search, $this->settings);
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
            throw new Exception('Did not retrieve DateSearchVariables from event dispatcher, got: ' . $event::class, 1657542318);
        }
        $this->view->assignMultiple($event->getVariablesForView());
        return $this->htmlResponse();
    }

    public function searchAction(array $search = []): ResponseInterface
    {
        $demand = $this->demandFactory->fromSettings($this->settings);
        if ($search !== []) {
            $demand = $this->demandFactory->createFromRequestValues($search, $this->settings);
        }

        $event = $this->eventDispatcher->dispatch(new DateSearchVariables(
            $this->settings,
            $search,
            $demand,
            $this->regionRepository->findAll(),
            $this->categoryRepository->findAllCurrentlyAssigned((int)($this->settings['uids']['categoriesParent'] ?? 0), 'categories'),
            $this->categoryRepository->findAllCurrentlyAssigned((int)($this->settings['uids']['featuresParent'] ?? 0), 'features')
        ));
        if (!$event instanceof DateSearchVariables) {
            throw new Exception('Did not retrieve DateSearchVariables from event dispatcher, got: ' . $event::class, 1657542318);
        }
        $this->view->assignMultiple($event->getVariablesForView());
        return $this->htmlResponse();
    }

    public function teaserAction(): ResponseInterface
    {
        $dates = $this->dateRepository->findByUids($this->settings['eventUids']);
        $this->view->assign('dates', $dates);
        return $this->htmlResponse();
    }

    #[Extbase\IgnoreValidation(['value' => 'date'])]
    public function showAction(?Date $date = null): ResponseInterface
    {
        if ($date === null) {
            $this->trigger404('No date given.');
        }

        try {
            $date->getEvent();
        } catch (Throwable) {
            $this->trigger404('No event found for requested date.');
        }

        $this->metaInformationService->setDate($date);
        $this->view->assign('date', $date);
        return $this->htmlResponse();
    }

    /**
     * Convert POST to proper GET.
     *
     * @see: https://en.wikipedia.org/wiki/Post/Redirect/Get
     */
    private function handlePostRequest(): void
    {
        if (
            $this->request->getMethod() === 'POST'
            && $this->request->hasArgument('search')
            && is_array($this->request->getArgument('search'))
        ) {
            $namespace = $this->extensionService->getPluginNamespace(null, null);
            $this->redirectToUri($this->request->getAttribute('currentContentObject')->typoLink_URL([
                'parameter' => 't3://page?uid=current',
                'additionalParams' => '&' . http_build_query([
                    $namespace => [
                        'search' => array_filter($this->request->getArgument('search')),
                    ],
                ]),
            ]));
        }
    }
}
