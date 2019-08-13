<?php

namespace Wrm\Events\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class CleanupService {

    /**
     * Cleanup Service constructor.
     * @param ConfigurationManager $configurationManager
     * @param ObjectManager $objectManager
     */
    public function __construct(
        ConfigurationManager $configurationManager,
        ObjectManager $objectManager
    ) {

        // Get Typoscript Settings
        $this->settings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'Events',
            'Pi1'
        );


        $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
        $this->logger->info('Event Cleanup Service');
    }

    public function doCleanup() {
        
        // To be done
        // Hmpf
    }
}