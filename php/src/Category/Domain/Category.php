<?php

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
        public readonly ?DateTimeImmutable $lastModified = null,
    ) {
        $this->id = $id ?? Uuid::uuid7();
        $this->subCategories = new ArrayCollection();

        $errors = $this->validate();
        if (!empty($errors)) {
            throw new CategoryNotValidException(json_encode($errors));
        }
    }

    /**
     * @throws SubCategoryNotBelongToCategoryException
     */
    public function update(self $category): self
    {
        $this->name = $category->name;
        $this->position = $category->position;

        foreach ($category->getSubCategories() as $subCategory) {
            if ($this->hasSubCategory($subCategory)) {
                $this->updateSubCategory($subCategory);
            } else {
                $this->addSubCategory($subCategory);
            }
        }

        foreach ($this->getSubCategories() as $subCategory) {
            if (!$category->hasSubCategory($subCategory)) {
                $this->removeSubCategory($subCategory);
            }
        }

        return $this;
    }

    public function hasSubCategory(Category $subCategory): bool
    {
        return isset($this->getSubCategories()[$subCategory->id]);
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

    /**
     * @return array<int, self>
     */
    public function getSubCategories(): array
    {
        return $this->subCategories->toArray();
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            $this->toSimpleArray(),
            [
                'subCategories' => array_map(
                    fn (self $category) => $category->toSimpleArray(),
                    $this->subCategories->toArray()
                ),
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
        $existedSubCategory = $this->getSubCategories()[$subCategory->id] ?? null;
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
            'lastModified' => $this->lastModified,
        ];
    }
}
