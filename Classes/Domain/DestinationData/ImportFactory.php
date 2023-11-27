<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Domain\DestinationData;

use Exception;
use PDO;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\Generic\Session;
use WerkraumMedia\Events\Domain\Model\Import;

final class ImportFactory
{
    /**
     * @var Folder
     */
    private $folderInstance;

    public function __construct(
        private readonly ConnectionPool $connectionPool,
        private readonly Session $extbasePersistenceSession,
        private readonly DataMapper $dataMapper,
        private readonly ResourceFactory $resourceFactory
    ) {
    }

    public function createFromUid(int $uid): Import
    {
        return $this->create($this->fetchImportRecord($uid));
    }

    /**
     * @return Import[]
     */
    public function createAll(): array
    {
        return array_map(
            $this->create(...),
            $this->fetchImportRecords()
        );
    }

    private function fetchImportRecord(int $uid): array
    {
        $qb = $this->connectionPool->getQueryBuilderForTable('tx_events_domain_model_import');
        $qb->select('*');
        $qb->from('tx_events_domain_model_import');
        $qb->where($qb->expr()->eq('uid', $qb->createNamedParameter($uid, PDO::PARAM_INT)));

        $result = $qb->executeQuery()->fetch();
        if (is_array($result) === false) {
            throw new Exception('Could not fetch import record with uid "' . $uid . '".', 1643267492);
        }

        $result = array_map('strval', $result);

        return $result;
    }

    private function fetchImportRecords(): array
    {
        $qb = $this->connectionPool->getQueryBuilderForTable('tx_events_domain_model_import');
        $qb->select('*');
        $qb->from('tx_events_domain_model_import');

        $result = $qb->executeQuery()->fetchAll();
        if (count($result) === 0) {
            throw new Exception('Could not fetch any import record.', 1643267492);
        }

        foreach ($result as $key => $entry) {
            $result[$key] = array_map('strval', $entry);
        }

        return $result;
    }

    private function create(array $data): Import
    {
        $this->createWorkarounds($data);

        $result = $this->dataMapper->map(Import::class, [$data])[0];

        $this->cleanupWorkarounds();

        return $result;
    }

    private function createWorkarounds(array $data): void
    {
        $this->folderInstance = $this->resourceFactory->getFolderObjectFromCombinedIdentifier($data['files_folder']);
        $this->extbasePersistenceSession->registerObject($this->folderInstance, $data['files_folder']);
    }

    private function cleanupWorkarounds(): void
    {
        $this->extbasePersistenceSession->unregisterObject($this->folderInstance);
    }
}
