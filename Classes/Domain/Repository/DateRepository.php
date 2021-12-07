<?php

namespace Wrm\Events\Domain\Repository;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use Wrm\Events\Domain\Model\Dto\DateDemand;
use Wrm\Events\Service\CategoryService;

class DateRepository extends Repository
{
    /**
     * @var Context
     */
    protected $context;

    public function injectContext(Context $context): void
    {
        $this->context = $context;
    }

    public function findByUids(string $uids): QueryResult
    {
        $uids = explode(',', $uids);
        $query = $this->createQuery();
        $query->matching(
            $query->in('uid', $uids)
        );
        return $query->execute();
    }

    public function findByDemand(DateDemand $demand): QueryResult
    {
        $query = $this->createDemandQuery($demand);
        return $query->execute();
    }

    protected function createDemandQuery(DateDemand $demand): QueryInterface
    {
        $query = $this->createQuery();
        $constraints = [];
        $categories = $demand->getCategories();

        if ($categories) {
            $categoryConstraints = $this->createCategoryConstraint($query, $categories, $demand->getIncludeSubCategories());
            if ($demand->getCategoryCombination() === 'or') {
                $constraints['categories'] = $query->logicalOr($categoryConstraints);
            } else {
                $constraints['categories'] = $query->logicalAnd($categoryConstraints);
            }
        }

        if ($demand->getRegion() !== '') {
            $constraints['region'] = $query->equals('event.region', $demand->getRegion());
        }

        if ($demand->getHighlight() !== false) {
            $constraints['highlight'] = $query->equals('event.highlight', $demand->getHighlight());
        }

        if ($demand->getSearchword() !== '') {
            $constraints['searchword'] = $this->getSearchwordConstraint($query, $demand);
        }

        if ($demand->getUserCategories() !== []) {
            $constraints['userCategories'] = $query->in('event.categories.uid', $demand->getUserCategories());
        }

        if ($demand->getStart() !== null) {
            $constraints['starts'] = $query->greaterThanOrEqual('start', $demand->getStart());
        }
        if ($demand->getEnd() != null) {
            // Dates might have end of 0 if only start exists.
            // This is respected to take start as end date.
            $constraints['ends'] = $query->logicalOr([
                $query->logicalAnd([
                    $query->lessThanOrEqual('end', $demand->getEnd()),
                    $query->greaterThan('end', 0)
                ]),
                $query->logicalAnd([
                    $query->equals('end', 0),
                    $query->lessThanOrEqual('start', $demand->getEnd())
                ]),
            ]);
        }

        if ($demand->getStart() === null && $demand->getEnd() === null) {
            $now = $this->context->getPropertyFromAspect(
                'date',
                'full',
                new \DateTimeImmutable()
            );
            $now = $now->modify('midnight');
            $constraints['nowAndFuture'] = $query->logicalOr([
                $query->greaterThanOrEqual('start', $now),
                $query->greaterThanOrEqual('end', $now)
            ]);
        }

        if ($demand->getLimit() !== '') {
            $query->setLimit((int) $demand->getLimit());
        }

        if (!empty($constraints)) {
            $query->matching($query->logicalAnd($constraints));
        }

        $query->setOrderings([$demand->getSortBy() => $demand->getSortOrder()]);

        $callback = $demand->getQueryCalback();
        if ($callback !== '') {
            $params = ['query' => &$query];
            GeneralUtility::callUserFunction($callback, $params, $this);
        }

        return $query;
    }

    private function getSearchwordConstraint(
        QueryInterface $query,
        DateDemand $demand
    ): ConstraintInterface {
        $fieldsToSearch = [
            'event.title',
            'event.teaser',
            'event.categories.title',
        ];

        $wordsToSearch = $demand->getSynonymsForSearchword();
        $wordsToSearch[] = $demand->getSearchword();
        $constraints = [];

        $queryBuilder = $this->objectManager->get(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_events_domain_model_date');

        foreach ($wordsToSearch as $word) {
            foreach ($fieldsToSearch as $field) {
                $constraints[] = $query->like($field, '%' . $queryBuilder->escapeLikeWildcards($word) . '%');
            }
        }

        return $query->logicalOr($constraints);
    }

    protected function createCategoryConstraint(QueryInterface $query, string $categories, bool $includeSubCategories = false): array
    {
        $constraints = [];

        if ($includeSubCategories) {
            $categoryService = GeneralUtility::makeInstance(CategoryService::class);
            $categories = $categoryService->getChildrenCategories($categories);
        }

        $categories = GeneralUtility::intExplode(',', $categories, true);
        foreach ($categories as $category) {
            $constraints[] = $query->contains('event.categories', $category);
        }
        return $constraints;
    }

    public function findSearchWord(string $search): array
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_events_domain_model_date');

        $queryBuilder = $connection->createQueryBuilder();

        $statement = $queryBuilder
            ->select('*')
            ->from('tx_events_domain_model_date')
            ->join(
                'tx_events_domain_model_date',
                'tx_events_domain_model_event',
                'event',
                $queryBuilder->expr()->eq('tx_events_domain_model_date.event', $queryBuilder->quoteIdentifier('event.uid'))
            )->where(
                $queryBuilder->expr()->like('event.title', $queryBuilder->createNamedParameter('%' . $search . '%'))
            )->orderBy('tx_events_domain_model_date.start');

        return $statement->execute()->fetchAll();
    }
}
