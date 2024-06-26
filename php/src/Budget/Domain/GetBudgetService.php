<?php

namespace App\Budget\Domain;

use App\Category\Domain\Category;
use App\Category\Domain\CategoryRepositoryInterface;
use App\Common\Domain\Period\PeriodUtil;
use DateInterval;
use DateTimeImmutable;

readonly class GetBudgetService
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
        private BudgetRepositoryInterface $budgetEntryRepository,
    ) {
    }

    public function execute(BudgetPeriod $period): Budget
    {
        // TODO also read from cache
        $budget = $this->budgetEntryRepository->findByPeriod($period);
        if (!empty($budget)) {
            return $budget;
        }

        $categories = $this->categoryRepository->findList();
        $budget = new Budget($period);
        
        foreach ($categories as $category) {
            $budgetEntry = new BudgetEntry(
                null,
                0,
                $category,
                $period->startDate,
            );
            $budget->addEntry($budgetEntry);

            foreach ($category->getSubCategories() as $subCategory) {
                $budgetEntry->addSubEntry(new BudgetEntry(
                    null,
                    0,
                    $subCategory,
                    $period->startDate,
                ));
            }
        }

        return $budget;
    }
}