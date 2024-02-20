<?php

namespace App\Expense\Domain;

interface ExpenseRepositoryInterface
{
    public function add(Expense $expense): void;

    public function remove(Expense $expense): void;
}
