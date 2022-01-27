<?php

namespace Wrm\Events\Domain\DestinationData;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\Generic\Session;
use Wrm\Events\Domain\Model\Import;

class ImportFactory
{
    /**
     * @var ConnectionPool
     */
    private $connectionPool;

    /**
     * @var Session
     */
    private $extbasePersistenceSession;

    /**
     * @var DataMapper
     */
    private $dataMapper;

    /**
     * @var ResourceFactory
     */
    private $resourceFactory;

    /**
     * @var Folder
     */
    private $folderInstance;

    public function __construct(
        ConnectionPool $connectionPool,
        Session $extbasePersistenceSession,
        DataMapper $dataMapper,
        ResourceFactory $resourceFactory
    ) {
        $this->connectionPool = $connectionPool;
        $this->extbasePersistenceSession = $extbasePersistenceSession;
        $this->dataMapper = $dataMapper;
        $this->resourceFactory = $resourceFactory;
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
            [$this, 'create'],
            $this->fetchImportRecords()
        );
    }

    private function fetchImportRecord(int $uid): array
    {
        $qb = $this->connectionPool->getQueryBuilderForTable('tx_events_domain_model_import');
        $qb->select('*');
        $qb->from('tx_events_domain_model_import');
        $qb->where($qb->expr()->eq('uid', $qb->createNamedParameter($uid, \PDO::PARAM_INT)));

        $result = $qb->execute()->fetch();
        if (is_array($result) === false) {
            throw new \Exception('Could not fetch import record with uid "' . $uid . '".', 1643267492);
        }

        $result = array_map('strval', $result);

        return $result;
    }

    private function fetchImportRecords(): array
    {
        $qb = $this->connectionPool->getQueryBuilderForTable('tx_events_domain_model_import');
        $qb->select('*');
        $qb->from('tx_events_domain_model_import');

        $result = $qb->execute()->fetchAll();
        if (count($result) === 0) {
            throw new \Exception('Could not fetch any import record.', 1643267492);
        }

        foreach ($result as $key => $entry) {
            $result[$key] = array_map('strval', $entry);
        }

        return $result;
    }

    /**
     * Only public in order to be used by LegacyImportFactory.
     * Make private once the class is removed.
     *
     * @internal
     */
    public function create(array $data): Import
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
