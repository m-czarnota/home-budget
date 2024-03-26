<?php

declare(strict_types=1);

namespace App\Expense\Domain;

readonly class RemoveCurrentExpenseService
{
    public function __construct(
        private CurrentExpenseRepositoryInterface $currentExpenseRepository,
    ) {
    }

    /**
     * @throws ExpenseNotFoundException
     */
    public function execute(string $currentExpenseId): void
    {
        $existingCurrentExpense = $this->currentExpenseRepository->findOneById($currentExpenseId);
        if (!$existingCurrentExpense) {
            throw new ExpenseNotFoundException("Current expense $currentExpenseId does not exist");
        }

        $this->currentExpenseRepository->remove($existingCurrentExpense);
    }
}
