<?php

namespace App\Budget\Application\UpdateBudget\Request;

class RequestBudgetEntryErrorInfoDto
{
    public function __construct(
        public bool $hasError = false,
        public ?string $id = null,
        public ?string $category = null,
        public ?string $cost = null,
        public ?string $plannedMonth = null,

        /** @var array<int, self> $subEntries */
        public array $subEntries = [],
    ) {
    }
}