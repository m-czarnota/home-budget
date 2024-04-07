<?php

declare(strict_types=1);

namespace App\Expense\Application\UpdateIrregularExpenses;

use App\Category\Domain\CategoryRepositoryInterface;
use App\Expense\Domain\ExpenseNotValidException;
use App\Expense\Domain\IrregularExpense;
use Symfony\Component\HttpFoundation\RequestStack;

readonly class RequestToModelsMapper
{
    public function __construct(
        private RequestStack $requestStack,
        private CategoryRepositoryInterface $categoryRepository,
    ) {
    }

    /**
     * @return array<int, IrregularExpense>
     *
     * @throws RequestNotValidException
     */
    public function execute(): array
    {
        $request = $this->requestStack->getCurrentRequest();
        $data = json_decode(trim($request->getContent()), true);

        if (empty($data)) {
            throw new RequestNotValidException('Sent request has not content');
        }

        $irregularExpenses = [];
        $requestErrorStates = [];
        $isError = false;

        foreach ($data as $irregularExpenseData) {
            $irregularExpenseErrorDto = new RequestIrregularExpenseErrorInfoDto();
            $requestErrorStates[] = $irregularExpenseErrorDto;

            try {
                $categoryId = $irregularExpenseData['category'] ?? '';
                $category = $this->categoryRepository->findOneById($categoryId);

                if ($category === null) {
                    $irregularExpenseErrorDto->hasError = $isError = true;
                    $irregularExpenseErrorDto->category = "Category `$categoryId` does not exist";
                    continue;
                }

                $irregularExpenses[] = new IrregularExpense(
                    $irregularExpenseData['id'] ?? null,
                    $irregularExpenseData['name'] ?? '',
                    $irregularExpenseData['cost'] ?? -1,
                    $category,
                    $irregularExpenseData['position'] ?? -1,
                    $irregularExpenseData['isWish'] ?? false,
                    $irregularExpenseData['plannedYear'] ?? null,
                );
            } catch (ExpenseNotValidException $exception) {
                $isError = true;
                $errors = json_decode($exception->getMessage(), true);

                $irregularExpenseErrorDto->hasError = true;
                $irregularExpenseErrorDto->name = $errors['name'] ?? null;
                $irregularExpenseErrorDto->position = $errors['position'] ?? null;
                $irregularExpenseErrorDto->cost = $errors['cost'] ?? null;
                $irregularExpenseErrorDto->plannedYear = $errors['plannedYear'] ?? null;
            }
        }

        if ($isError) {
            throw new RequestNotValidException(json_encode($requestErrorStates));
        }

        return $irregularExpenses;
    }
}
