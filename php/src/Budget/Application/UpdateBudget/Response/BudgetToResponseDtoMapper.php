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
                fn(BudgetEntry $entry) => self::mapBudgetEntryToBudgetEntryResponseDto($entry),
                $budget->getEntries()
            ),
        );
    }

    private static function mapBudgetEntryToBudgetEntryResponseDto(BudgetEntry $budgetEntry): BudgetEntryResponseDto
    {
        return new BudgetEntryResponseDto(
            $budgetEntry->id,
            $budgetEntry->getCost(),
            $budgetEntry->category->id,
            $budgetEntry->category->getName(),
            array_map(
                fn(BudgetEntry $subEntry) => self::mapBudgetEntryToBudgetEntryResponseDto($subEntry),
                $budgetEntry->getSubEntries()
            ),
        );
    }
}