<?php

declare(strict_types=1);

namespace App\Expense\Domain;

readonly class UpdateIrregularExpensesService
{
    public function __construct(
        private IrregularExpenseRepositoryInterface $irregularExpenseRepository,
    ) {
    }

    /**
     * @return array<int, IrregularExpense>
     */
    public function execute(IrregularExpense ...$irregularExpenses): array
    {
        $responseIrregularExpenses = array_map(function (IrregularExpense $irregularExpense) {
            $existedIrregularExpense = $this->irregularExpenseRepository->findOneById($irregularExpense->id);
            if (!$existedIrregularExpense) {
                $this->irregularExpenseRepository->add($irregularExpense);

                return $irregularExpense;
            }

            $existedIrregularExpense->update($irregularExpense);

            return $existedIrregularExpense;
        }, $irregularExpenses);

        $this->irregularExpenseRepository->removeNotInList(...$responseIrregularExpenses);

        return $responseIrregularExpenses;
    }
}
