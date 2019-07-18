<?php
namespace Wrm\Events\Domain\Repository;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Wrm\Events\Domain\Model\Dto\DateDemand;
use Wrm\Events\Domain\Model\Event;
use Wrm\Events\Service\CategoryService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

class DateRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    /**
     * Find all products based on selected uids
     *
     * @param string $uids
     *
     * @return array
     */
    public function findByUids($uids)
    {
        $uids = explode(',', $uids);

        $query = $this->createQuery();
        //$query->getQuerySettings()->setRespectStoragePage(false);

        $query->matching(
            $query->in('uid', $uids)
        );

        //return $this->orderByField($query->execute(), $uids);

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

        // For testing purposes
        // $query = $this->createDemandQueryViaBuilder($demand);

        return $query->execute();
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

        if ($demand->getRegion() !== null) {
            $constraints['highlight'] = $query->equals('event.highlight', $demand->getHighlight());
        }

        if ($demand->getLimit() !== '') {
            $query->setLimit((int) $demand->getLimit());
        }


        if (!empty($constraints)) {
            $query->matching($query->logicalAnd($constraints));
        }


        $sortBy = $demand->getSortBy();
        if ($sortBy && $sortBy !== 'singleSelection' && $sortBy !== 'default') {
            $order = strtolower($demand->getSortOrder()) === 'desc' ? QueryInterface::ORDER_DESCENDING : QueryInterface::ORDER_ASCENDING;
            $query->setOrderings([$sortBy => $order]);
        }

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
     * @param DateDemand
     * @return $statement
     * @throws InvalidQueryException
     */

    protected function createDemandQueryViaBuilder(DateDemand $demand) {

        //$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_Events_domain_model_date');

        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_Events_domain_model_date');

        $queryBuilder = $connection->createQueryBuilder();

        $statement = $queryBuilder
            ->select('tx_Events_domain_model_date.start', 'tx_Events_domain_model_date.end', 'tx_Events_domain_model_date.event')
            ->from('tx_Events_domain_model_date')
            ->join(
                'tx_Events_domain_model_date',
                'tx_Events_domain_model_event',
                'event',
                $queryBuilder->expr()->eq('tx_Events_domain_model_date.event', $queryBuilder->quoteIdentifier('event.uid'))
            )->where(
                $queryBuilder->expr()->eq('event.title', $queryBuilder->createNamedParameter('Bachführung'))
            );

        if ($demand->getLimit() !== '') {
            $statement->setMaxResults((int) $demand->getLimit());
        }

        return $statement;
    }

}