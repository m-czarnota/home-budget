<?php

namespace App\Tests\Expense\Behat;

use App\Category\Domain\CategoryRepositoryInterface;
use App\Expense\Domain\ExpenseNotValidException;
use App\Expense\Domain\IrregularExpense;
use App\Expense\Domain\IrregularExpenseRepositoryInterface;
use App\Tests\Common\HomeBudgetKernelBrowser;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Step\Given;
use Behat\Step\Then;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Assert;

readonly class IrregularExpenseContext implements Context
{
    public function __construct(
        private HomeBudgetKernelBrowser $browser,
        private IrregularExpenseRepositoryInterface $irregularExpenseRepository,
        private CategoryRepositoryInterface $categoryRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws ExpenseNotValidException
     */
    #[Given('there are exist irregular expenses with')]
    public function thereAreExistIrregularExpenses(PyStringNode $irregularExpensesContent): void
    {
        $irregularExpensesData = json_decode(trim($irregularExpensesContent->getRaw()), true);

        foreach ($irregularExpensesData as $irregularExpenseData) {
            $existedIrregularExpense = $this->irregularExpenseRepository->findOneById($irregularExpenseData['id']);
            if ($existedIrregularExpense) {
                continue;
            }

            $category = $this->categoryRepository->findOneById($irregularExpenseData['category']);

            $irregularExpense = new IrregularExpense(
                $irregularExpenseData['id'],
                $irregularExpenseData['name'],
                1000,
                $category,
                0,
            );
            $this->irregularExpenseRepository->add($irregularExpense);
        }

        $this->entityManager->flush();
    }

    #[Then('in db there are only irregular expenses received in response')]
    public function inDbThereAreOnlyIrregularExpensesReceivedInResponse(): void
    {
        $irregularExpenseIdsFromDb = array_map(
            fn (IrregularExpense $irregularExpense) => $irregularExpense->id,
            $this->irregularExpenseRepository->findList()
        );
        $irregularExpenseIdsFromResponse = array_flip(array_map(
            fn (array $irregularExpenseData) => $irregularExpenseData['id'],
            $this->browser->getLastResponseContentAsArray()
        ));

        foreach ($irregularExpenseIdsFromDb as $idFromDb) {
            Assert::assertArrayHasKey($idFromDb, $irregularExpenseIdsFromResponse);
        }
    }
}
