<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Domain\Repository;

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
use WerkraumMedia\Events\Domain\Model\Dto\EventDemand;
use WerkraumMedia\Events\Domain\Model\Event;

final class EventRepository extends Repository
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

        $settings = $query->getQuerySettings();
        if (array_filter($settings->getStoragePageIds()) === []) {
            $settings->setRespectStoragePage(false);
        }

        $query = $this->setOrderings($query, $demand);

        $constraints = $this->getConstraints($query, $demand);
        if (!empty($constraints)) {
            $query->matching($query->logicalAnd(... $constraints));
        }

        if ($demand->getLimit() !== '') {
            $query->setLimit((int)$demand->getLimit());
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

        $query->setOrderings([$sortBy => QueryInterface::ORDER_ASCENDING]);

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

    /**
     * @return ConstraintInterface[]
     */
    private function getConstraints(QueryInterface $query, EventDemand $demand): array
    {
        $constraints = [];

        if ($demand->getSearchword() !== '') {
            $constraints['searchword'] = $this->createSearchwordConstraint($query, $demand);
        }

        if ($demand->getCategories() !== []) {
            $constraints['categories'] = $this->createCategoryConstraint($query, $demand->getCategories());
        }

        if ($demand->getUserCategories() !== []) {
            $constraints['userCategories'] = $this->createCategoryConstraint($query, $demand->getUserCategories());
        }

        if ($demand->getRecordUids() !== []) {
            $constraints['recordUids'] = $query->in('uid', $demand->getRecordUids());
        }

        if ($demand->getHighlight()) {
            $constraints['highlight'] = $query->equals('highlight', $demand->getHighlight());
        }

        return $constraints;
    }

    /**
     * Free-text over the event's text fields; a match in any one qualifies.
     */
    protected function createSearchwordConstraint(QueryInterface $query, EventDemand $demand): ConstraintInterface
    {
        $searchword = '%' . $demand->getSearchword() . '%';

        return $query->logicalOr(
            $query->like('title', $searchword),
            $query->like('subtitle', $searchword),
            $query->like('teaser', $searchword),
            $query->like('details', $searchword),
        );
    }

    /**
     * OR-combined: the event matches if it carries ANY of the given categories.
     * Editor scope and visitor picks each build one such group; the two groups
     * are AND-combined by {@see getConstraints()} (refine-within semantics).
     *
     * @param int[] $categories
     */
    protected function createCategoryConstraint(QueryInterface $query, array $categories): ConstraintInterface
    {
        $constraints = [];
        foreach ($categories as $category) {
            $constraints[] = $query->contains('categories', $category);
        }

        return $query->logicalOr(... $constraints);
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
