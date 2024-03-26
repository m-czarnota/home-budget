<?php

declare(strict_types=1);

namespace App\Expense\Domain;

readonly class ListIrregularExpensesService
{
    public function __construct(
        private IrregularExpenseRepositoryInterface $irregularExpenseRepository,
    ) {
    }

    /**
     * @return array<int, IrregularExpense>
     */
    public function execute(): array
    {
        return $this->irregularExpenseRepository->findList();
    }
}
