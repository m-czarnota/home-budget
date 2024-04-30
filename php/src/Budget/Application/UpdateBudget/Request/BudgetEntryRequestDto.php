<?php

namespace App\Budget\Application\UpdateBudget\Request;

class BudgetEntryRequestDto
{
    public function __construct(
        public string $id,
        public string $categoryId,
        public float $cost,
        public int $plannedMonth,
    ) {
    }
}