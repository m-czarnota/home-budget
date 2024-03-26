<?php

declare(strict_types=1);

namespace App\Expense\Domain;

use App\Category\Domain\Category;

interface CurrentExpenseRepositoryInterface
{
    public function add(CurrentExpense $currentExpense): void;

    public function remove(CurrentExpense $currentExpense): void;

    public function findOneById(string $id): ?CurrentExpense;

    public function hasCategoryAnyConnection(Category $category): bool;
}
