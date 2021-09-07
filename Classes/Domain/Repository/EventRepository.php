<?php

namespace Wrm\Events\Domain\Repository;

/*
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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use Wrm\Events\Domain\Model\Dto\EventDemand;
use Wrm\Events\Domain\Model\Event;
use Wrm\Events\Service\CategoryService;

class EventRepository extends Repository
{
    public function findByUids(string $uids): QueryResult
    {
        $query = $this->createQuery();
        $query->matching($query->in('uid', GeneralUtility::intExplode(',', $uids)));

        return $query->execute();
    }

    /**
     * @return QueryResult|array
     */
    public function findByDemand(EventDemand $demand)
    {
        $query = $this->createDemandQuery($demand);

        if ($demand->getRecordUids() !== [] && $demand->getSortBy() === 'default') {
            return $this->sortByDemand($query, $demand);
        }

        return $query->execute();
    }

    protected function createDemandQuery(EventDemand $demand): QueryInterface
    {
        $query = $this->createQuery();
        $query = $this->setOrderings($query, $demand);

        $constraints = $this->getConstraints($query, $demand);
        if (!empty($constraints)) {
            $query->matching($query->logicalAnd($constraints));
        }

        if ($demand->getLimit() !== '') {
            $query->setLimit((int) $demand->getLimit());
        }

        return $query;
    }

    private function setOrderings(QueryInterface $query, EventDemand $demand): QueryInterface
    {
        $sortBy = $demand->getSortBy();
        $sortingsToIgnore = ['singleSelection', 'default'];

        if (!$sortBy || in_array($sortBy, $sortingsToIgnore)) {
            return $query;
        }

        $order = QueryInterface::ORDER_ASCENDING;
        if (strtolower($demand->getSortOrder()) === 'desc') {
            $order = QueryInterface::ORDER_DESCENDING;
        }

        $query->setOrderings([$sortBy => $order]);

        return $query;
    }

    private function sortByDemand(QueryInterface $query, EventDemand $demand): array
    {
        $result = $query->execute()->toArray();
        $expectedSorting = $demand->getRecordUids();

        usort($result, function (Event $eventA, Event $eventB) use ($expectedSorting) {
            $positionOfA = array_search($eventA->getUid(), $expectedSorting);
            if ($positionOfA === false) {
                $positionOfA = array_search($eventA->getLocalizedUid(), $expectedSorting);
            }

            $positionOfB = array_search($eventB->getUid(), $expectedSorting);
            if ($positionOfB === false) {
                $positionOfB = array_search($eventB->getLocalizedUid(), $expectedSorting);
            }

            return $positionOfA <=> $positionOfB;
        });

        return $result;
    }

    private function getConstraints(QueryInterface $query, EventDemand $demand): array
    {
        $constraints = [];

        if ($demand->getCategories()) {
            $constraints['categories'] = $this->createCategoryConstraint($query, $demand);
        }

        if ($demand->getRecordUids() !== []) {
            $constraints['recordUids'] = $query->in('uid', $demand->getRecordUids());
        }

        if ($demand->getRegion() !== '') {
            $constraints['region'] = $query->equals('region', $demand->getRegion());
        }

        if ($demand->getHighlight()) {
            $constraints['highlight'] = $query->equals('highlight', $demand->getHighlight());
        }

        return $constraints;
    }

    protected function createCategoryConstraint(QueryInterface $query, EventDemand $demand): ConstraintInterface
    {
        $constraints = [];

        $categories = $demand->getCategories();
        if ($demand->getIncludeSubCategories()) {
            $categoryService = GeneralUtility::makeInstance(CategoryService::class);
            $categories = $categoryService->getChildrenCategories($categories);
        }

        $categories = GeneralUtility::intExplode(',', $categories, true);
        foreach ($categories as $category) {
            $constraints[] = $query->contains('categories', $category);
        }

        if ($demand->getCategoryCombination() === 'or') {
            return $query->logicalOr($constraints);
        }
        return $query->logicalAnd($constraints);
    }

    public function findSearchWord(string $search): QueryResult
    {
        $query = $this->createQuery();
        $query->matching($query->like('title', '%' . $search . '%'));
        $query->setOrderings(['title' => QueryInterface::ORDER_ASCENDING]);
        $query->setLimit(20);
        return $query->execute();
    }
}
