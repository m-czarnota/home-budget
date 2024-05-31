<?php

namespace App\Budget\Application\UpdateBudget\Response;

use App\Budget\Application\UpdateBudget\Request\RequestBudgetEntryErrorInfoDto;

class BadRequestResponse
{
    public readonly bool $hasError;

    public function __construct(
        public ?string $month = null,

        /** @var array<int, RequestBudgetEntryErrorInfoDto> $entries */
        public array $entries = [],
    ) {
        $this->hasError = true;
    }
}