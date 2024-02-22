<?php

namespace App\Expense\Domain;

interface ExpenseRepositoryInterface
{
    public function add(CurrentExpense $expense): void;

    public function remove(CurrentExpense $expense): void;
}
