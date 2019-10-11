<?php
namespace Wrm\Events\Domain\Repository;

use Wrm\Events\Domain\Model\Dto\DateDemand;
use Wrm\Events\Service\CategoryService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

class DateRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    /**
     * Find all dates based on selected uids
     * @param string $uids
     * @return array
     */
    public function findByUids($uids)
    {
        $uids = explode(',', $uids);
        $query = $this->createQuery();
        $query->matching(
            $query->in('uid', $uids)
        );
        return $query->execute();
    }

    /**
     * @param DateDemand $demand
     * @return QueryResultInterface
     * @throws InvalidQueryException
     */
    public function findByDemand(DateDemand $demand)
    {
        $query = $this->createDemandQuery($demand);
        return $query->execute();

        // For testing purposes
        // $query = $this->createDemandQueryViaBuilder($demand);
        //return $query->execute()->fetchAll();
    }

    /**
     * @param DateDemand $demand
     * @return QueryInterface
     * @throws InvalidQueryException
     */
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

        if ($demand->getHighlight() !== FALSE) {
            $constraints['highlight'] = $query->equals('event.highlight', $demand->getHighlight());
        }

        if ($demand->getSearchword() !== '') {
            $constraints['searchword'] = $query->logicalOr(
                [
                    $query->like('event.title', '%' . $demand->getSearchword() . '%'),
                    $query->like('event.teaser', '%' . $demand->getSearchword() . '%')
                ]
            );
        }

        if ($demand->getStart() !== '' && $demand->getEnd() != '') {
            $constraints['daterange'] = $query->logicalAnd(
                [
                    $query->greaterThanOrEqual('start', $demand->getStart()),
                    $query->lessThanOrEqual('start', $demand->getEnd())
                ]
            );
        } else {
            $now = new \DateTime('now', new \DateTimeZone('Europe/Berlin'));
            $constraints['untilnow'] = $query->greaterThanOrEqual('start', $now);
        }

        if ($demand->getLimit() !== '') {
            $query->setLimit((int) $demand->getLimit());
        }

        if (!empty($constraints)) {
            $query->matching($query->logicalAnd($constraints));
        }

        $query->setOrderings([$demand->getSortBy() => $demand->getSortOrder()]);

        return $query;
    }

    /**
     * @param QueryInterface $query
     * @param string $categories
     * @param bool $includeSubCategories
     * @return array
     * @throws InvalidQueryException
     */
    protected function createCategoryConstraint(QueryInterface $query, $categories, bool $includeSubCategories = false): array
    {
        $constraints = [];

        if ($includeSubCategories) {
            $categoryService = GeneralUtility::makeInstance(CategoryService::class);
            $allCategories = $categoryService->getChildrenCategories($categories);
            if (!\is_array($allCategories)) {
                $allCategories = GeneralUtility::intExplode(',', $allCategories, true);
            }
        } else {
            $allCategories = GeneralUtility::intExplode(',', $categories, true);
        }

        foreach ($allCategories as $category) {
            $constraints[] = $query->contains('event.categories', $category);
        }
        return $constraints;
    }

    /**
     * findSearchWord with Query Builder
     * @param $search
     */
    public function findSearchWord($search)
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