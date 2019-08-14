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

use Wrm\Events\Domain\Model\Dto\EventDemand;
use Wrm\Events\Service\CategoryService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

class EventRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
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
     * @param EventDemand $demand
     * @return QueryResultInterface
     * @throws InvalidQueryException
     */
    public function findByDemand(EventDemand $demand)
    {
        $query = $this->createDemandQuery($demand);
        return $query->execute();
    }

    /**
     * @param EventDemand $demand
     * @return QueryInterface
     * @throws InvalidQueryException
     */
    protected function createDemandQuery(EventDemand $demand): QueryInterface
    {
        $query = $this->createQuery();

        // sorting
        $sortBy = $demand->getSortBy();
        if ($sortBy && $sortBy !== 'singleSelection' && $sortBy !== 'default') {
            $order = strtolower($demand->getSortOrder()) === 'desc' ? QueryInterface::ORDER_DESCENDING : QueryInterface::ORDER_ASCENDING;
            $query->setOrderings([$sortBy => $order]);
        }

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
            $constraints['region'] = $query->equals('region', $demand->getRegion());
        }

        if ($demand->getHighlight()) {
            $constraints['highlight'] = $query->equals('highlight', $demand->getHighlight());
        }

        if ($demand->getLimit() !== '') {
            $query->setLimit((int) $demand->getLimit());
        }

        if (!empty($constraints)) {
            $query->matching($query->logicalAnd($constraints));
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
            $constraints[] = $query->contains('categories', $category);
        }
        return $constraints;
    }


    public function findSearchWord($search)
    {
        $query = $this->createQuery();
        $query->matching(
            $query->like('title', '%' . $search . '%')
        );
        $query->setOrderings(['title' => QueryInterface::ORDER_ASCENDING]);
        $query->setLimit(20);
        return $query->execute();
    }

}