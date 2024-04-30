<?php

namespace App\Budget\Application\UpdateBudget\Request;

use App\Budget\Application\UpdateBudget\Response\BadRequestResponse;
use App\Budget\Domain\Budget;
use App\Budget\Domain\BudgetEntry;
use App\Budget\Domain\BudgetEntryIsNotValidException;
use App\Budget\Domain\BudgetPeriod;
use App\Budget\Domain\InvalidBudgetPeriodException;
use App\Category\Domain\CategoryRepositoryInterface;
use DateTime;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\RequestStack;

readonly class RequestToModelsMapper
{
    public function __construct(
        private RequestStack $requestStack,
        private CategoryRepositoryInterface $categoryRepository,
    ) {
    }

    /**
     * @return Budget
     * @throws InvalidBudgetPeriodException
     * @throws RequestNotValidException
     */
    public function execute(): Budget
    {
        $request = $this->requestStack->getCurrentRequest();
        $data = json_decode(trim($request->getContent()), true);

        if (empty($data)) {
            throw new RequestNotValidException('Sent request data is empty');
        }

        $badRequestResponse = new BadRequestResponse();
        $month = intval($data['month']);

        if ($month < 1 || $month > 12) {
            $badRequestResponse->monthError = "Month `$month` is not valid month number. Allowed months are in range 1-12.";
        }

        $budgetStartDate = new DateTime();
        $budgetStartDate
            ->setDate($budgetStartDate->format('Y'), $month, 1)
            ->setTime(0, 0, 0, 0);

        $budgetEntries = [];
        $isBudgetEntryError = [];

        foreach ($data as $budgetEntryData) {
            $budgetEntryErrorIntoDto = new RequestBudgetEntryErrorInfoDto();
            $badRequestResponse->errors[] = $budgetEntryErrorIntoDto;

            try {
                $categoryId = $budgetEntryData['category'] ?? '';
                $category = $this->categoryRepository->findOneById($categoryId);

                if ($category === null) {
                    $budgetEntryErrorIntoDto->hasError = $isBudgetEntryError = true;
                    $budgetEntryErrorIntoDto->category = "Category `$categoryId` does not exist";
                    continue;
                }

                $budgetEntries[] = new BudgetEntry(
                    $budgetEntryData['id'] ?? null,
                    $budgetEntryData['cost'] ?? null,
                    $category,
                    DateTimeImmutable::createFromInterface($budgetStartDate),
                    null,
                );
            } catch (BudgetEntryIsNotValidException $exception) {
                $isBudgetEntryError = true;
                $errors = json_decode($exception->getMessage(), true);

                $budgetEntryErrorIntoDto->hasError = true;
                $budgetEntryErrorIntoDto->id = $errors['id'] ?? null;
                $budgetEntryErrorIntoDto->cost = $errors['cost'] ?? null;
            }
        }

        if ($isBudgetEntryError || $badRequestResponse->monthError) {
            throw new RequestNotValidException(json_encode($badRequestResponse));
        }

        $budgetEndDate = DateTime::createFromInterface($budgetStartDate);
        $budgetEndDate->modify('last day of this month');

        $budgetPeriod = new BudgetPeriod(
            DateTimeImmutable::createFromInterface($budgetStartDate),
            DateTimeImmutable::createFromInterface($budgetEndDate)
        );
        $budget = new Budget($budgetPeriod);
        foreach ($budgetEntries as $budgetEntry) {
            $budget->addEntry($budgetEntry);
        }

        return $budget;
    }
}