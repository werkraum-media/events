<?php

namespace Wrm\Events\Controller;

use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Service\ExtensionService;
use Wrm\Events\Domain\Model\Date;
use Wrm\Events\Domain\Model\Dto\DateDemand;
use Wrm\Events\Domain\Model\Dto\DateDemandFactory;
use Wrm\Events\Domain\Repository\CategoryRepository;
use Wrm\Events\Domain\Repository\DateRepository;
use Wrm\Events\Domain\Repository\RegionRepository;
use Wrm\Events\Events\Controller\DateListVariables;
use Wrm\Events\Events\Controller\DateSearchVariables;
use Wrm\Events\Pagination\Factory;
use Wrm\Events\Service\DataProcessingForModels;

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
     * @var Factory
     */
    protected $paginationFactory;

    /**
     * @var DataProcessingForModels
     */
    protected $dataProcessing;

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var ExtensionService
     */
    protected $extensionService;

    public function __construct(
        DateDemandFactory $demandFactory,
        DateRepository $dateRepository,
        RegionRepository $regionRepository,
        CategoryRepository $categoryRepository,
        Factory $paginationFactory,
        DataProcessingForModels $dataProcessing,
        EventDispatcher $eventDispatcher,
        ExtensionService $extensionService
    ) {
        $this->demandFactory = $demandFactory;
        $this->dateRepository = $dateRepository;
        $this->regionRepository = $regionRepository;
        $this->categoryRepository = $categoryRepository;
        $this->paginationFactory = $paginationFactory;
        $this->dataProcessing = $dataProcessing;
        $this->eventDispatcher = $eventDispatcher;
        $this->extensionService = $extensionService;
    }

    protected function initializeAction(): void
    {
        $contentObject = $this->configurationManager->getContentObject();
        if ($contentObject !== null) {
            $this->demandFactory->setContentObjectRenderer($contentObject);
        }
        $this->dataProcessing->setConfigurationManager($this->configurationManager);

        $this->handlePostRequest();
    }

    /**
     * @param array $search
     * @param int $currentPage
     */
    public function listAction(
        array $search = [],
        int $currentPage = 1
    ): void {
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
            throw new \Exception('Did not retrieve DateSearchVariables from event dispatcher, got: ' . get_class($event), 1657542318);
        }
        $this->view->assignMultiple($event->getVariablesForView());
    }

    /**
     * @param array $search
     */
    public function searchAction(array $search = []): void
    {
        $demand = $this->demandFactory->fromSettings($this->settings);
        if ($search !== []) {
            $demand = $this->demandFactory->createFromRequestValues($search, $this->settings);
        }

        $event = $this->eventDispatcher->dispatch(new DateSearchVariables(
            $search,
            $demand,
            $this->regionRepository->findAll(),
            $this->categoryRepository->findAllCurrentlyAssigned($this->settings['uids']['categoriesParent'] ?? 0, 'categories'),
            $this->categoryRepository->findAllCurrentlyAssigned($this->settings['uids']['featuresParent'] ?? 0, 'features')
        ));
        if (!$event instanceof DateSearchVariables) {
            throw new \Exception('Did not retrieve DateSearchVariables from event dispatcher, got: ' . get_class($event), 1657542318);
        }
        $this->view->assignMultiple($event->getVariablesForView());
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
            $this->redirectToUri($this->configurationManager->getContentObject()->typoLink_URL([
                'parameter' => 't3://page?uid=current',
                'additionalParams' => '&' . http_build_query([
                    $namespace => [
                        'search' => array_filter($this->request->getArgument('search'))
                    ],
                ]),
            ]));
        }
    }
}
