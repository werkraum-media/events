<?php

namespace WerkraumMedia\Events\Service\DestinationDataImportService;

use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use WerkraumMedia\Events\Domain\Model\Category;
use WerkraumMedia\Events\Domain\Repository\CategoryRepository;
use WerkraumMedia\Events\Service\DestinationDataImportService\CategoriesAssignment\Import;

/**
 * Provides APIs to work with categories that will be assigned to events during import.
 *
 * Categories mean TYPO3 sys_category records.
 * Those are used for multiple records within destination data. E.g. categories or features.
 */
class CategoriesAssignment
{
    /**
     * @var CategoryRepository
     */
    private $repository;

    /**
     * @var PersistenceManager
     */
    private $persistenceManager;

    public function __construct(
        CategoryRepository $repository,
        PersistenceManager $persistenceManager
    ) {
        $this->repository = $repository;
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * @return ObjectStorage<Category>
     */
    public function getCategories(
        Import $import
    ): ObjectStorage {
        $categories = new ObjectStorage();

        if ($import->getParentCategory() === null || $import->getPid() === null) {
            return $categories;
        }

        foreach ($import->getCategoryTitles() as $categoryTitle) {
            $category = $this->repository->findOneForImport(
                $import->getParentCategory(),
                $import->getPid(),
                $categoryTitle
            );

            if (!$category instanceof Category) {
                $category = new Category(
                    $import->getParentCategory(),
                    $import->getPid(),
                    $categoryTitle,
                    $import->getHideByDefault() ? true : false
                );
                $this->repository->add($category);
            }

            $categories->attach($category);
        }

        $this->persistenceManager->persistAll();

        return $categories;
    }
}
