<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Service;

use RuntimeException;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\TimeTracker\TimeTracker;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class CategoryService
{
    private readonly TimeTracker $timeTracker;

    private readonly FrontendInterface $cache;

    public function __construct()
    {
        $this->timeTracker = GeneralUtility::makeInstance(TimeTracker::class);
        $this->cache = GeneralUtility::makeInstance(CacheManager::class)->getCache('events_category');
    }

    /**
     * Get child categories by calling recursive function
     * and using the caching framework to save some queries
     *
     * @param string $idList list of category ids to start
     *
     * @return string comma separated list of category ids
     */
    public function getChildrenCategories($idList, int $counter = 0): string
    {
        $cacheIdentifier = sha1('children' . $idList);

        $entry = $this->cache->get($cacheIdentifier);
        if (!$entry || is_string($entry) === false) {
            $entry = $this->getChildrenCategoriesRecursive($idList, $counter);
            $this->cache->set($cacheIdentifier, $entry);
        }

        return $entry;
    }

    /**
     * Get child categories
     *
     * @param string $idList list of category ids to start
     * @param int $counter
     *
     * @return string comma separated list of category ids
     */
    protected function getChildrenCategoriesRecursive($idList, $counter = 0): string
    {
        $result = [];

        // add id list to the output
        if ($counter === 0) {
            $newList = $this->getUidListFromRecords($idList);
            if ($newList) {
                $result[] = $newList;
            }
        }

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('sys_category')
        ;
        $res = $queryBuilder
            ->select('uid')
            ->from('sys_category')
            ->where($queryBuilder->expr()->in(
                'parent',
                $queryBuilder->createNamedParameter(explode(',', $idList), Connection::PARAM_INT_ARRAY)
            ))
            ->executeQuery()
        ;

        foreach ($res->fetchAllAssociative() as $row) {
            $counter++;
            if ($counter > 10000) {
                $this->timeTracker->setTSlogMessage('EXT:dd_events: one or more recursive categories where found');
                return implode(',', $result);
            }
            $uid = $row['uid'];
            if (is_numeric($uid) === false) {
                throw new RuntimeException('Given uid was not numeric, which we never expect as UID column within DB is numeric.', 1728998121);
            }

            $subcategories = $this->getChildrenCategoriesRecursive((string)$uid, $counter);
            $result[] = $row['uid'] . ($subcategories ? ',' . $subcategories : '');
        }

        $result = implode(',', $result);
        return $result;
    }

    /**
     * Fetch ids again from DB to avoid false positives
     */
    protected function getUidListFromRecords(string $idList): string
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('sys_category')
        ;
        $uids = $queryBuilder
            ->select('uid')
            ->from('sys_category')
            ->where($queryBuilder->expr()->in(
                'uid',
                $queryBuilder->createNamedParameter(explode(',', $idList), Connection::PARAM_INT_ARRAY)
            ))
            ->executeQuery()
            ->fetchFirstColumn()
        ;

        return implode(',', $uids);
    }
}
