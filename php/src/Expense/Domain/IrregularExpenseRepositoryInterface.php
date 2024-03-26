<?php

declare(strict_types=1);

namespace App\Expense\Domain;

use App\Category\Domain\Category;

interface IrregularExpenseRepositoryInterface
{
    public function add(IrregularExpense $irregularExpense): void;

    public function remove(IrregularExpense $irregularExpense): void;

    public function findOneById(string $id): ?IrregularExpense;

    /**
     * @return array<int, IrregularExpense>
     */
    public function findList(): array;

    public function removeNotInList(IrregularExpense ...$irregularExpenses): void;

    public function hasCategoryAnyConnection(Category $category): bool;
}
