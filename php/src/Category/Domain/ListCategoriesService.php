<?php

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
        return $this->categoryRepository->findList();
    }
}
