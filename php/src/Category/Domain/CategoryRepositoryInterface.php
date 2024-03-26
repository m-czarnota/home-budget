<?php

declare(strict_types=1);

namespace App\Category\Domain;

use InvalidArgumentException;

interface CategoryRepositoryInterface
{
    /**
     * @throws InvalidArgumentException
     */
    public function add(Category $category): void;

    public function remove(Category $category): void;

    /**
     * @throws InvalidArgumentException
     */
    public function update(Category $category): void;

    public function findOneById(string $id): ?Category;

    /**
     * @return array<int, Category>
     */
    public function findList(): array;

    /**
     * @return array<int, Category>
     */
    public function findAll(): array;
}
