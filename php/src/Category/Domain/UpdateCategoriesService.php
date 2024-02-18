<?php

namespace App\Category\Domain;

readonly class UpdateCategoriesService
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
    ) {
    }

    /**
     * @return array<int, Category>
     *
     * @throws SubCategoryNotBelongToCategoryException
     */
    public function execute(Category ...$categories): array
    {
        $responseCategories = array_map(function (Category $category) {
            $existedCategory = $this->categoryRepository->findOneById($category->id);
            if (!$existedCategory) {
                $this->categoryRepository->add($category);

                return $category;
            }

            $existedCategory->update($category);
            $this->categoryRepository->update($existedCategory);

            return $existedCategory;
        }, $categories);

        return $responseCategories;
    }
}
