<?php

namespace App\Budget\Application\UpdateBudget\Response;

class BudgetResponseDto
{
    public function __construct(
        public int $month,

        /** @var array<int, BudgetEntryResponseDto> $entries */
        public array $entries = [],
    ) {
    }
}