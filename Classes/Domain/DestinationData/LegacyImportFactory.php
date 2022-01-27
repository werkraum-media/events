<?php

namespace Wrm\Events\Domain\DestinationData;

use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Session;
use Wrm\Events\Domain\Model\Import;

/**
 * This is only for legacy imports where folder is not configured by TCA and provided in TYPO3 way,
 * but provided as plain string which was resolved to default storyage within import service.
 *
 * Should be dropped once old import command is dropped.
 */
class LegacyImportFactory
{
    /**
     * @var ImportFactory
     */
    private $importFactory;

    /**
     * @var ResourceFactory
     */
    private $resourceFactory;

    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @var Session
     */
    private $extbasePersistenceSession;

    public function __construct(
        ImportFactory $importFactory,
        ResourceFactory $resourceFactory,
        ConfigurationManager $configurationManager,
        Session $extbasePersistenceSession
    ) {
        $this->importFactory = $importFactory;
        $this->resourceFactory = $resourceFactory;
        $this->configurationManager = $configurationManager;
        $this->extbasePersistenceSession = $extbasePersistenceSession;
    }

    public function createFromArray(array $configuration): Import
    {
        $result = array_map('strval', $configuration);

        $result['uid'] = $this->getUniqueUid();

        $result['files_folder'] = $this->migrateFileFolder($result['files_folder'] ?? '');

        $result['region'] = $result['region_uid'];
        unset($result['region_uid']);

        $result = $this->addCategorySettings($result);

        return $this->importFactory->create($result);
    }

    private function getUniqueUid(): string
    {
        do {
            // Only temporary solution as long as legacy exists.
            // Cool solution would be to fetch highest uid + 100, but that's to much for now.
            // Also this will vanish in future.
            $uid = (string) random_int(999, PHP_INT_MAX);
        } while ($this->extbasePersistenceSession->hasIdentifier($uid, Import::class));

        return $uid;
    }

    private function migrateFileFolder(string $fileFolder): string
    {
        $storage = $this->resourceFactory->getDefaultStorage();
        if ($storage === null) {
            throw new \Exception('No default storage defined. Cancel import.', 1643290642);
        }

        $uid = $storage->getUid();

        return $uid . ':/' . trim($fileFolder, '/') . '/';
    }

    private function addCategorySettings(array $result): array
    {
        $settings = $this->configurationManager->getConfiguration(
            ConfigurationManager::CONFIGURATION_TYPE_SETTINGS,
            'Events',
            'Pi1'
        );

        $result['categories_pid'] = $settings['destinationData']['categoriesPid'] ?? '';
        $result['category_parent'] = $settings['destinationData']['categoryParentUid'] ?? '';

        return $result;
    }
}
