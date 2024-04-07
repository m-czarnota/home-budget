<?php

namespace App\Expense\Application\UpdateIrregularExpenses;

use JsonSerializable;

class RequestIrregularExpenseErrorInfoDto implements JsonSerializable
{
    public function __construct(
        public bool $hasError = false,
        public ?string $name = null,
        public ?string $position = null,
        public ?string $cost = null,
        public ?string $plannedYear = null,
        public ?string $category = null,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'hasError' => $this->hasError,
            'name' => $this->name,
            'position' => $this->position,
            'cost' => $this->cost,
            'plannedYear' => $this->plannedYear,
            'category' => $this->category,
        ];
    }
}