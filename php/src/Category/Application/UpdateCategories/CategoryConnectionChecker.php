<?php

declare(strict_types=1);

namespace App\Category\Application\UpdateCategories;

use App\Category\Domain\Category;
use App\Category\Domain\CategoryDeletionInfo;
use App\Category\Domain\CategoryDeletionInfoCollection;
use App\Category\Domain\CategoryRepositoryInterface;
use App\Expense\Domain\CurrentExpenseRepositoryInterface;
use App\Expense\Domain\IrregularExpenseRepositoryInterface;

readonly class CategoryConnectionChecker
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
        private CurrentExpenseRepositoryInterface $currentExpenseRepository,
        private IrregularExpenseRepositoryInterface $irregularExpenseRepository,
    ) {
    }

    public function execute(): CategoryDeletionInfoCollection
    {
        $categories = $this->categoryRepository->findAll();
        $categoryDeletionInfoCollection = new CategoryDeletionInfoCollection();

        foreach ($categories as $category) {
            $categoryDeletionInfo = new CategoryDeletionInfo(
                $category->id,
                !$this->hasCategoryAnyConnection($category),
            );
            $categoryDeletionInfoCollection->add($categoryDeletionInfo);

            foreach ($category->getSubCategories() as $subCategory) {
                $subCategoryDeletionInfo = new CategoryDeletionInfo(
                    $subCategory->id,
                    !$this->hasCategoryAnyConnection($subCategory),
                );
                $categoryDeletionInfo->addSubCategoryDeletionInfo($subCategoryDeletionInfo);
            }
        }

        return $categoryDeletionInfoCollection;
    }

    private function hasCategoryAnyConnection(Category $category): bool
    {
        $hasCurrentExpenseConnection = $this->currentExpenseRepository->hasCategoryAnyConnection($category);
        $hasIrregularExpenseConnection = $this->irregularExpenseRepository->hasCategoryAnyConnection($category);

        return $hasCurrentExpenseConnection || $hasIrregularExpenseConnection;
    }
}
