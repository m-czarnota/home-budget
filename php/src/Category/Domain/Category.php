<?php

declare(strict_types=1);

namespace App\Category\Domain;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JsonSerializable;
use Ramsey\Uuid\Uuid;

class Category implements JsonSerializable
{
    public readonly string $id;

    private bool $isDeleted = false;

    public readonly DateTimeImmutable $lastModified;

    private ?self $parent = null;

    /** @var Collection<int, self> */
    private Collection $subCategories;

    /**
     * @throws CategoryNotValidException
     */
    public function __construct(
        ?string $id,
        private string $name,
        private int $position = 0,
        ?DateTimeImmutable $lastModified = null,
    ) {
        $this->id = $id ?? Uuid::uuid7()->toString();
        $this->lastModified = $lastModified ?? new DateTimeImmutable();
        $this->subCategories = new ArrayCollection();

        $errors = $this->validate();
        if (!empty($errors)) {
            throw new CategoryNotValidException(json_encode($errors));
        }
    }

    /**
     * Updates this category and their subcategories.
     *
     * Removes subcategories from this category which don't exist in updated category.
     * Marks as deleted removed subcategories based on deletion info.
     *
     * @throws SubCategoryNotBelongToCategoryException
     */
    public function update(self $category, ?CategoryDeletionInfo $categoryDeletionInfo = null): self
    {
        // update this category
        $this->name = $category->name;
        $this->position = $category->position;

        // adding new subcategories or update existing
        foreach ($category->getSubCategories() as $subCategory) {
            if ($this->hasSubCategory($subCategory)) {
                $this->updateSubCategory($subCategory);
            } else {
                $this->addSubCategory($subCategory);
            }
        }

        // removing subcategories that not exist in updated category
        foreach ($this->getSubCategories() as $subCategory) {
            if ($category->hasSubCategory($subCategory)) {
                continue;
            }

            $this->removeSubCategory($subCategory);

            // mark as deleted in various conditions if deletion info is provided
            if ($categoryDeletionInfo) {
                $subCategoryDeletionInfo = $categoryDeletionInfo->getSubCategory($subCategory->id);
                $subCategory->markAsDeletedIfAllowed($subCategoryDeletionInfo);
            }
        }

        return $this;
    }

    /**
     * Mark category as deleted if category should be removed, but they cannot be removed because it isn't deletable.
     * Also, the method marks subcategories of this category in the same way.
     *
     * @param CategoryDeletionInfo $categoryDeletionInfo contains data for this category and their subcategories about deletion
     *
     * @return $this
     */
    public function markAsDeletedIfAllowed(CategoryDeletionInfo $categoryDeletionInfo): self
    {
        assert($this->id === $categoryDeletionInfo->id, 'Ids must be the same');

        foreach ($categoryDeletionInfo->getSubCategories() as $subCategoryDeletionInfo) {
            $subCategory = $this->subCategories[$subCategoryDeletionInfo->id] ?? null;
            assert($subCategory !== null, "Subcategory doesn't exist");

            $subCategory->markAsDeletedIfAllowed($subCategoryDeletionInfo);
        }

        $this->isDeleted = !$categoryDeletionInfo->isDeletable();

        return $this;
    }

    public function hasSubCategory(Category $subCategory): bool
    {
        return $this->subCategories->get($subCategory->id) !== null;
    }

    public function addSubCategory(self $subCategory): self
    {
        $this->subCategories->set($subCategory->id, $subCategory);

        return $this;
    }

    public function removeSubCategory(self $subCategory): self
    {
        $this->subCategories->remove($subCategory->id);

        return $this;
    }

    public function findSubCategory(string $id): ?self
    {
        return $this->subCategories->get($id);
    }

    /**
     * @return array<int, self>
     */
    public function getSubCategories(bool $withKeys = false): array
    {
        return $withKeys ? $this->subCategories->toArray() : $this->subCategories->getValues();
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            $this->toSimpleArray(),
            [
                'subCategories' => array_values(array_map(
                    fn (self $category) => $category->toSimpleArray(),
                    $this->subCategories->toArray()
                )),
            ],
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    /**
     * @throws SubCategoryNotBelongToCategoryException
     */
    private function updateSubCategory(self $subCategory): self
    {
        $existedSubCategory = $this->subCategories->get($subCategory->id);
        if (!$existedSubCategory) {
            throw new SubCategoryNotBelongToCategoryException("Subcategory {$subCategory->id} doesn't belong to category {$this->id}");
        }

        $existedSubCategory->update($subCategory);

        return $this;
    }

    private function validate(): array
    {
        $errors = [];

        if (empty($this->name)) {
            $errors['name'] = 'Name cannot be empty';
        }
        if (strlen($this->name) > 255) {
            $errors['name'] = 'Name cannot be longer than 255 characters';
        }

        if ($this->position < 0) {
            $errors['position'] = 'Position cannot be negative';
        }

        return $errors;
    }

    private function toSimpleArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'position' => $this->position,
            'isDeleted' => $this->isDeleted,
            'lastModified' => $this->lastModified->format('Y-m-d H:i:s'),
        ];
    }
}
