<?php

namespace App\Expense\Domain;

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
}
