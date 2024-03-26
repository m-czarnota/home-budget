<?php

declare(strict_types=1);

namespace App\Category\Domain;

readonly class ListCategoriesService
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
    ) {
    }

    /**
     * @return array<int, Category>
     */
    public function execute(): array
    {
        $categories = $this->categoryRepository->findList();
        $listCategories = [];

        foreach ($categories as $category) {
            if ($category->isDeleted()) {
                continue;
            }

            foreach ($category->getSubCategories() as $subCategory) {
                if ($subCategory->isDeleted()) {
                    $category->removeSubCategory($subCategory);
                }
            }

            $listCategories[] = $category;
        }

        return $listCategories;
    }
}
