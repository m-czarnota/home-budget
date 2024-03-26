<?php

declare(strict_types=1);

namespace App\Category\Domain;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

class CategoryDeletionInfoCollection implements Countable, IteratorAggregate
{
    /** @var array<string, CategoryDeletionInfo> */
    private array $categories = [];

    /**
     * @return Traversable<string, CategoryDeletionInfo>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->categories);
    }

    public function count(): int
    {
        return count($this->categories);
    }

    public function add(CategoryDeletionInfo $categoryDeletionInfo): self
    {
        $this->categories[$categoryDeletionInfo->id] = $categoryDeletionInfo;

        return $this;
    }

    public function get(string $id): ?CategoryDeletionInfo
    {
        return $this->categories[$id] ?? null;
    }
}
