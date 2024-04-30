<?php

namespace App\Budget\Application\UpdateBudget\Response;

class BudgetEntryResponseDto
{
    public function __construct(
        public string $id,
        public float $cost,
        public string $categoryId,
        public string $categoryName,
    )
    {
    }
}