<?php

namespace App\Budget\Domain;

readonly class UpdateBudgetService
{
    public function __construct(
        private BudgetRepositoryInterface $budgetRepository,
    ) {
    }

    public function execute(Budget $budget): Budget
    {
        $existedBudget = $this->budgetRepository->findByPeriod($budget->period);

        if ($existedBudget) {
            $existedBudget->update($budget);
            $this->budgetRepository->update($existedBudget);
        } else {
            $this->budgetRepository->add($budget);
        }

        return $budget;
    }
}