<?php

namespace App\Expense\Application\AddCurrentExpense;

use App\Category\Domain\CategoryNotFoundException;
use App\Category\Domain\CategoryRepositoryInterface;
use App\Expense\Domain\CurrentExpense;
use App\Expense\Domain\ExpenseNotValidException;
use App\Person\Domain\PersonNotFoundException;
use App\Person\Domain\PersonRepositoryInterface;
use DateTimeImmutable;
use Exception;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\RequestStack;

readonly class RequestToModelMapper
{
    public function __construct(
        private RequestStack $requestStack,
        private CategoryRepositoryInterface $categoryRepository,
        private PersonRepositoryInterface $personRepository,
    ) {
    }

    /**
     * @throws ExpenseNotValidException
     * @throws CategoryNotFoundException
     * @throws InvalidArgumentException
     * @throws PersonNotFoundException
     */
    public function execute(): CurrentExpense
    {
        $request = $this->requestStack->getCurrentRequest();
        $data = json_decode(trim($request->getContent()), true);

        $categoryId = $data['category'] ?? '';
        $category = $this->categoryRepository->findOneById($categoryId);

        if ($category === null) {
            $error = json_encode(['category' => "Category `$categoryId` does not exist"]);
            throw new CategoryNotFoundException($error);
        }

        $peopleErrors = [];
        $people = [];
        foreach ($data['people'] as $personId) {
            $person = $this->personRepository->findOneById($personId);

            if ($person) {
                $people[] = $person;
            } else {
                $peopleErrors[] = "Person `$personId` does not exist";
            }
        }

        if (!empty($peopleErrors)) {
            throw new PersonNotFoundException(json_encode(['people' => $peopleErrors]));
        }

        $dateOfExpenseData = $data['dateOfExpense'] ?? null;
        try {
            $dateOfExpense = $dateOfExpenseData ? new DateTimeImmutable($dateOfExpenseData) : null;
        } catch (Exception) {
            $error = json_encode(['dateOfExpense' => "Date of expense `$dateOfExpenseData` is not valid date time"]);
            throw new InvalidArgumentException($error);
        }

        $currentExpense = new CurrentExpense(
            null,
            $data['name'],
            $data['cost'],
            $category,
            $data['isWish'],
            $dateOfExpense,
        );

        foreach ($people as $person) {
            $currentExpense->addPerson($person);
        }

        return $currentExpense;
    }
}
