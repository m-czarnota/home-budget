<?php

namespace App\Budget\Application\UpdateBudget\Response;

use App\Budget\Domain\Budget;
use App\Budget\Domain\BudgetEntry;

readonly class BudgetToResponseDtoMapper
{
    public static function execute(Budget $budget): BudgetResponseDto
    {
        return new BudgetResponseDto(
            intval($budget->period->startDate->format('m')),  // broken demeter law,
            array_map(
                fn(BudgetEntry $entry) => new BudgetEntryResponseDto(
                    $entry->id,
                    $entry->getCost(),
                    $entry->category->id,
                    $entry->category->getName(),
                ),
                $budget->getEntries()
            ),
        );
    }
}