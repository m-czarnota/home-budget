<?php

declare(strict_types=1);

namespace App\Tests\Expense\Behat;

use App\Category\Domain\CategoryRepositoryInterface;
use App\Expense\Domain\CurrentExpense;
use App\Expense\Domain\CurrentExpenseRepositoryInterface;
use App\Expense\Domain\ExpenseNotValidException;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Step\Given;
use Doctrine\ORM\EntityManagerInterface;

readonly class CurrentExpenseContext implements Context
{
    public function __construct(
        private CurrentExpenseRepositoryInterface $currentExpenseRepository,
        private CategoryRepositoryInterface $categoryRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws ExpenseNotValidException
     */
    #[Given('there are exist current expenses with')]
    public function thereAreExistCurrentExpenses(PyStringNode $currentExpensesContent): void
    {
        $currentExpensesData = json_decode(trim($currentExpensesContent->getRaw()), true);

        foreach ($currentExpensesData as $currentExpenseData) {
            $existedCurrentExpense = $this->currentExpenseRepository->findOneById($currentExpenseData['id']);
            if ($existedCurrentExpense) {
                continue;
            }

            $category = $this->categoryRepository->findOneById($currentExpenseData['category']);

            $currentExpense = new CurrentExpense(
                $currentExpenseData['id'],
                $currentExpenseData['name'],
                1000,
                $category,
            );
            $this->currentExpenseRepository->add($currentExpense);
        }

        $this->entityManager->flush();
    }
}
