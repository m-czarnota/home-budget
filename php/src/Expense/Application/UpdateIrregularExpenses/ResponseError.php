<?php

declare(strict_types=1);

namespace App\Expense\Application\UpdateIrregularExpenses;

class ResponseError
{
    public readonly bool $isError;

    public function __construct(
        public array $errors = [],
    ) {
        $this->isError = true;
    }
}
