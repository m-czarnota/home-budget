<?php

declare(strict_types=1);

namespace App\Category\Domain;

class CategoryDeletionInfo
{
    private array $subCategories = [];

    /**
     * @param bool $canBeDeleted determines if category can be deleted without taking into account the subcategories
     */
    public function __construct(
        public readonly string $id,
        private readonly bool $canBeDeleted,
    ) {
    }

    /**
     * Method returns true only when all subcategories will be deletable and the category will be able to be deleted too.
     */
    public function isDeletable(): bool
    {
        foreach ($this->subCategories as $subCategory) {
            if (!$subCategory->isDeletable()) {
                return false;
            }
        }

        return $this->canBeDeleted;
    }

    public function addSubCategoryDeletionInfo(self $subCategoryDeletionInfo): self
    {
        $this->subCategories[$subCategoryDeletionInfo->id] = $subCategoryDeletionInfo;

        return $this;
    }

    /**
     * @return array<string, self>
     */
    public function getSubCategories(): array
    {
        return $this->subCategories;
    }

    public function getSubCategory(string $id): ?self
    {
        return $this->subCategories[$id] ?? null;
    }
}
