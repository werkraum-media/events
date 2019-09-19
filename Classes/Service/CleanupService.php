<?php

namespace Wrm\Events\Service;

use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CleanupService
{
    public function deleteAllData()
    {
        $this->truncateTables(... [
            'tx_events_domain_model_date',
            'tx_events_domain_model_organizer',
        ]);

        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        /* @var DataHandler $dataHandler */
        $dataHandler->start([], $this->getDeletionStructure([
            'tx_events_domain_model_event',
        ]));
        $dataHandler->process_cmdmap();
    }

    public function deletePastData()
    {
        $this->deleteDates(... $this->getPastDates());

        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        /* @var DataHandler $dataHandler */
        $dataHandler->start([], $this->getDeletionStructureForEventsWithoutDates());
        $dataHandler->process_cmdmap();
    }

    private function truncateTables(string ...$tableNames): void
    {
        foreach ($tableNames as $tableName) {
            GeneralUtility::makeInstance(ConnectionPool::class)
                ->getConnectionForTable($tableName)
                ->truncate($tableName);
        }
    }

    private function getDeletionStructure(array $tableNames): array
    {
        $structure = [];

        foreach ($tableNames as $tableName) {
            $structure = array_merge($structure, $this->getDeletionStructureForTable($tableName));
        }

        return $structure;
    }

    private function getDeletionStructureForTable(string $tableName): array
    {
        $dataStructure = [$tableName=> []];

        foreach ($this->getRecordsToDelete($tableName) as $recordToDelete) {
            $dataStructure[$tableName][$recordToDelete] = ['delete' => 1];
        }

        return $dataStructure;
    }

    private function getRecordsToDelete(string $tableName): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable($tableName)
            ->createQueryBuilder();

        /* @var QueryBuilder $queryBuilder */
        $records = $queryBuilder->select('uid')
            ->from($tableName)
            ->execute()
            ->fetchAll();

        return array_map(function (array $record) {
            return $record['uid'];
        }, $records);
    }

    private function getPastDates(): array
    {
        $midnightToday = new \DateTimeImmutable('midnight today');

        /* @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_events_domain_model_date')
            ->createQueryBuilder();

        $queryBuilder->getRestrictions()->removeAll();

        $records = $queryBuilder->select('uid')
            ->from('tx_events_domain_model_date')
            ->where($queryBuilder->expr()->lte(
                'end',
                $queryBuilder->createNamedParameter($midnightToday->format('Y-m-d H:i:s'))
            ))
            ->execute()
            ->fetchAll();

        return array_map(function (array $record) {
            return $record['uid'];
        }, $records);
    }

    private function deleteDates(int ...$uids)
    {
        /* @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_events_domain_model_date');

        $queryBuilder->delete('tx_events_domain_model_date')
            ->where('uid in (:uids)')
            ->setParameter(':uids', $uids, Connection::PARAM_INT_ARRAY)
            ->execute();
    }

    private function getDeletionStructureForEventsWithoutDates(): array
    {
        $dataStructure = ['tx_events_domain_model_event' => []];
        /* @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_events_domain_model_event')
            ->createQueryBuilder();

        $queryBuilder->getRestrictions()->removeAll();

        $records = $queryBuilder->select('event.uid')
            ->from('tx_events_domain_model_event', 'event')
            ->leftJoin('event', 'tx_events_domain_model_date', 'date', $queryBuilder->expr()->eq('date.event', 'event.uid'))
            ->where($queryBuilder->expr()->isNull('date.uid'))
            ->execute()
            ->fetchAll();

        foreach ($records as $record) {
            $dataStructure['tx_events_domain_model_event'][$record['uid']] = ['delete' => 1];
        }
        return $dataStructure;
    }
}
