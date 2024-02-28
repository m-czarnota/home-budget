<?php

namespace App\Expense\Domain;

readonly class AddCurrentExpenseService
{
    public function __construct(
        private CurrentExpenseRepositoryInterface $currentExpenseRepository,
    ) {
    }

    /**
     * @throws ExpenseNotValidException
     */
    public function execute(CurrentExpense $currentExpense): CurrentExpense
    {
        if (empty($currentExpense->getPeople())) {
            throw new ExpenseNotValidException('The current expense must have at least one person assigned to it');
        }

        $this->currentExpenseRepository->add($currentExpense);

        return $currentExpense;
    }
}
