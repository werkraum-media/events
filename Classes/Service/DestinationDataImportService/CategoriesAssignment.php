<?php

namespace Wrm\Events\Service\DestinationDataImportService;

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use Wrm\Events\Domain\Model\Category;
use Wrm\Events\Domain\Repository\CategoryRepository;
use Wrm\Events\Service\DestinationDataImportService\CategoriesAssignment\Import;

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

    public function __construct(
        CategoryRepository $repository
    ) {
        $this->repository = $repository;
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
                $category = new Category();
                $category->setParent($import->getParentCategory());
                $category->setPid($import->getPid());
                $category->setTitle($categoryTitle);
                if ($import->getHideByDefault()) {
                    $category->hide();
                }
                $this->repository->add($category);
            }

            $categories->attach($category);
        }

        return $categories;
    }
}
