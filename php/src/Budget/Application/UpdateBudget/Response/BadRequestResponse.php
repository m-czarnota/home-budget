<?php

namespace App\Budget\Application\UpdateBudget\Response;

use App\Budget\Application\UpdateBudget\Request\RequestBudgetEntryErrorInfoDto;

class BadRequestResponse
{
    public readonly bool $hasError;

    public function __construct(
        public ?string $monthError = null,

        /** @var array<int, RequestBudgetEntryErrorInfoDto> $errors */
        public array $errors = [],
    ) {
        $this->hasError = true;
    }
}