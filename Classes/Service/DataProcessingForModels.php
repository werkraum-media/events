<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Service;

/*
 * Copyright (C) 2021 Daniel Siepmann <coding@daniel-siepmann.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapFactory;
use TYPO3\CMS\Frontend\ContentObject\ContentDataProcessor;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Used by models to apply data processing.
 * This allows for flexibility of integrators.
 *
 * E.g. pages are saved for each event.
 * An integrator now can resolve them via data processing to arrays or menus.
 *
 * dataProcessing is configured via TypoScript for each plugin or whole extension via
 * settings.dataProcessing.fqcn, e.g.:
 *
 *     plugin.tx_events {
 *         settings {
 *             dataProcessing {
 *                 WerkraumMedia\Events\Domain\Model\Event {
 *                     10 = TYPO3\CMS\Frontend\DataProcessing\MenuProcessor
 *                     10 {
 *                         special = list
 *                         special.value.field = pages
 *                         dataProcessing {
 *                             10 = TYPO3\CMS\Frontend\DataProcessing\FilesProcessor
 *                             10 {
 *                                 references.fieldName = media
 *                             }
 *                         }
 *                     }
 *                 }
 *             }
 *         }
 *     }
 *
 * Currently supported by:
 *
 * - Event->getPages()
 */
final class DataProcessingForModels implements SingletonInterface
{
    private readonly ContentObjectRenderer $cObject;

    private readonly Connection $connection;

    private ?ConfigurationManagerInterface $configurationManager = null;

    public function __construct(
        private readonly ContentDataProcessor $processorHandler,
        ConnectionPool $connectionPool,
        private readonly DataMapFactory $dataMapFactory,
        private readonly TypoScriptService $typoScriptService
    ) {
        $this->cObject = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $this->connection = $connectionPool->getConnectionByName('Default');
    }

    /**
     * Used to set current configuration from within plugin.
     *
     * Inject and call this method, e.g. within initializeAction.
     * Necessary to get plugin configuration containing dataProcessing configuration.
     */
    public function setConfigurationManager(ConfigurationManagerInterface $configurationManager): void
    {
        $this->configurationManager = $configurationManager;
    }

    public function process(
        AbstractEntity $entity
    ): array {
        $configuration = $this->getConfiguration($entity);

        if ($configuration === []) {
            return [];
        }

        $this->cObject->start($this->getData($entity), $this->getTable($entity));
        return $this->processorHandler->process($this->cObject, $configuration, []);
    }

    private function getData(AbstractEntity $entity): array
    {
        $row = $this->connection->select(['*'], $this->getTable($entity), ['uid' => $entity->getUid()])->fetch();
        if (is_array($row)) {
            return $row;
        }

        return [];
    }

    private function getTable(AbstractEntity $entity): string
    {
        $dataMap = $this->dataMapFactory->buildDataMap($entity::class);
        return $dataMap->getTableName();
    }

    private function getConfiguration(AbstractEntity $entity): array
    {
        if ($this->configurationManager === null) {
            return [];
        }

        $className = $entity::class;
        $settings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS
        );

        if (ArrayUtility::isValidPath($settings, 'dataProcessing.' . $className, '.') === false) {
            return [];
        }

        $configuration = ArrayUtility::getValueByPath($settings, 'dataProcessing.' . $className, '.');
        if (is_array($configuration) === false) {
            return [];
        }

        $configuration = $this->typoScriptService->convertPlainArrayToTypoScriptArray($configuration);
        return [
            'dataProcessing.' => $configuration,
        ];
    }
}
