<?php

declare(strict_types=1);

namespace App\Category\Domain;

readonly class UpdateCategoriesService
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
    ) {
    }

    /**
     * @var CategoryDeletionInfoCollection Should contain all categories in the system
     * @var Category                       ...$categories
     *
     * @return array<int, Category>
     *
     * @throws SubCategoryNotBelongToCategoryException
     */
    public function execute(CategoryDeletionInfoCollection $categoryDeletionInfoCollection, Category ...$categories): array
    {
        $responseCategories = array_map(function (Category $category) use ($categoryDeletionInfoCollection) {
            $existedCategory = $this->categoryRepository->findOneById($category->id);
            if (!$existedCategory) {
                $this->categoryRepository->add($category);

                return $category;
            }

            $categoryDeletionInfo = $categoryDeletionInfoCollection->get($existedCategory->id);
            $existedCategory->update($category, $categoryDeletionInfo);

            $this->categoryRepository->update($existedCategory);

            return $existedCategory;
        }, $categories);

        // todo move below logic to event
        $responseCategoriesIds = array_flip(array_map(fn (Category $category) => $category->id, $responseCategories));

        // removing categories that have been removed by user
        foreach ($categoryDeletionInfoCollection as $categoryDeletionInfo) {
            if (isset($responseCategoriesIds[$categoryDeletionInfo->id])) {
                continue;
            }

            $category = $this->categoryRepository->findOneById($categoryDeletionInfo->id);
            $category->markAsDeletedIfAllowed($categoryDeletionInfo);  // mark as deleted if category cannot be removed
            $this->categoryRepository->remove($category);
        }

        return $responseCategories;
    }
}
