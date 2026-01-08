<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Service\DestinationDataImportService;

use Doctrine\DBAL\ArrayParameterType;
use RuntimeException;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class Cleanup
{
    public function __construct(
        private readonly DataHandler $dataHandler,
        private readonly ConnectionPool $connectionPool,
    ) {
    }

    public function removeNoLongerExistingEvents(int $importUid, string ... $globalIds): void
    {
        $eventUidsToRemove = $this->determineEventUidsToRemove($importUid, ... $globalIds);
        $this->dataHandler->removeEvents(... $eventUidsToRemove);
    }

    /**
     * @return int[]
     */
    private function determineEventUidsToRemove(int $importUid, string ... $globalIds): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tx_events_domain_model_event');
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        $queryBuilder->select('uid');
        $queryBuilder->from('tx_events_domain_model_event');
        $queryBuilder->where($queryBuilder->expr()->eq(
            'import_configuration',
            $queryBuilder->createNamedParameter($importUid)
        ));
        $queryBuilder->andWhere($queryBuilder->expr()->notIn(
            'global_id',
            $queryBuilder->createNamedParameter($globalIds, ArrayParameterType::STRING)
        ));

        $uids = [];
        foreach ($queryBuilder->executeQuery()->fetchFirstColumn() as $uid) {
            if (is_numeric($uid) === false) {
                throw new RuntimeException('Given UID was not numeric, this should never happen.', 1768215835);
            }

            $uids[] = (int)$uid;
        }

        return $uids;
    }
}
