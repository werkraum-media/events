<?php

declare(strict_types=1);

/*
 * Copyright (C) 2023 Daniel Siepmann <coding@daniel-siepmann.de>
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

namespace WerkraumMedia\Events\Service\DestinationDataImportService;

use Exception;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Resource\DuplicationBehavior;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Index\MetaDataRepository;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use WerkraumMedia\Events\Domain\Model\Event;
use WerkraumMedia\Events\Domain\Model\Import;

class FilesAssignment
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var DataFetcher
     */
    private $dataFetcher;

    /**
     * @var ResourceFactory
     */
    private $resourceFactory;

    /**
     * @var MetaDataRepository
     */
    private $metaDataRepository;

    public function __construct(
        LogManager $logManager,
        DataFetcher $dataFetcher,
        ResourceFactory $resourceFactory,
        MetaDataRepository $metaDataRepository
    ) {
        $this->logger = $logManager->getLogger(self::class);
        $this->dataFetcher = $dataFetcher;
        $this->resourceFactory = $resourceFactory;
        $this->metaDataRepository = $metaDataRepository;
    }

    /**
     * @return ObjectStorage<FileReference>
     */
    public function getImages(
        Import $import,
        Event $event,
        array $assets
    ): ObjectStorage {
        $images = new ObjectStorage();
        $importFolder = $import->getFilesFolder();

        foreach ($assets as $mediaObject) {
            if ($this->isImage($mediaObject) === false) {
                continue;
            }

            $fileUrl = urldecode($mediaObject['url']);
            $orgFileNameSanitized = $importFolder->getStorage()->sanitizeFileName(basename($fileUrl));

            $this->logger->info('File attached.', [$fileUrl, $orgFileNameSanitized]);

            if ($importFolder->hasFile($orgFileNameSanitized)) {
                $this->logger->info('File already exists.', [$orgFileNameSanitized]);
            } elseif ($filename = $this->loadFile($fileUrl)) {
                $this->logger->info('Adding file to FAL.', [$filename]);
                $importFolder->addFile($filename, basename($fileUrl), DuplicationBehavior::REPLACE);
            } else {
                continue;
            }

            if ($importFolder->hasFile($orgFileNameSanitized) === false) {
                $this->logger->warning('Could not find file.', [$orgFileNameSanitized]);
                continue;
            }

            $file = $importFolder->getStorage()->getFileInFolder($orgFileNameSanitized, $importFolder);
            if (!$file instanceof File) {
                $this->logger->warning('Could not find file.', [$orgFileNameSanitized]);
                continue;
            }

            $this->updateMetadata($file, $mediaObject);
            $images->attach($this->getFileReference($event, $file, $mediaObject));
        }

        return $images;
    }

    private function loadFile(string $fileUrl): string
    {
        $this->logger->info('Getting file.', [$fileUrl]);

        try {
            $response = $this->dataFetcher->fetchImage($fileUrl);
        } catch (Exception $e) {
            $this->logger->error('Cannot load file.', [$fileUrl]);
            return '';
        }

        if ($response->getStatusCode() !== 200) {
            $this->logger->error('Cannot load file.', [$fileUrl]);
            return '';
        }

        $file = new \SplFileInfo($fileUrl);
        $temporaryFilename = GeneralUtility::tempnam($file->getBasename());
        $writeResult = GeneralUtility::writeFile($temporaryFilename, $response->getBody()->__toString(), true);
        if ($writeResult === false) {
            $this->logger->error('Could not write temporary file.', [$temporaryFilename]);
            return '';
        }

        return $temporaryFilename;
    }

    private function updateMetadata(
        File $file,
        array $mediaObject
    ): void {
        $this->metaDataRepository->update($file->getUid(), [
            'title' => $this->getShortenedString($mediaObject['value'], 100),
            'description' => $mediaObject['description'] ?? '',
            'alternative' => $mediaObject['description'] ?? '',
            'creator_tool' => 'destination.one',
            'source' => $mediaObject['url'] ?? '',
        ]);
    }

    private function getFileReference(
        Event $event,
        File $file,
        array $mediaObject
    ): FileReference {
        foreach ($event->getImages() as $existingRelation) {
            if ($existingRelation->getOriginalResource()->getOriginalFile() === $file) {
                return $existingRelation;
            }
        }

        return $this->createFileReference($event, $file, $mediaObject);
    }

    private function createFileReference(
        Event $event,
        File $file,
        array $mediaObject
    ): FileReference {
        $coreReference = $this->resourceFactory->createFileReferenceObject([
            'uid' => uniqid('NEW_'),
            'uid_local' => $file->getUid(),
            'uid_foreign' => $event->getUid(),
        ]);
        $extbaseReference = new FileReference();
        $extbaseReference->setOriginalResource($coreReference);
        return $extbaseReference;
    }

    private function getShortenedString(string $string, int $lenght): string
    {
        if ($string === mb_substr($string, 0, $lenght)) {
            return $string;
        }

        return mb_substr($string, 0, $lenght - 3) . ' …';
    }

    private function isImage(array $mediaObject): bool
    {
        $allowedMimeTypes = [
            'image/jpeg',
            'image/png',
        ];

        return ((string)$mediaObject['rel']) === 'default'
            && in_array($mediaObject['type'], $allowedMimeTypes)
        ;
    }
}
