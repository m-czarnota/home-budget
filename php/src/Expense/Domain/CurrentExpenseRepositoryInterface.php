<?php

namespace App\Expense\Domain;

interface CurrentExpenseRepositoryInterface
{
    public function add(CurrentExpense $currentExpense): void;

    public function remove(CurrentExpense $currentExpense): void;
}
