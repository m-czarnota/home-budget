<?php

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
        $errors = [];
        $isError = false;

        foreach ($data as $irregularExpenseData) {
            try {
                $categoryId = $irregularExpenseData['category'] ?? '';
                $category = $this->categoryRepository->findOneById($categoryId);

                if ($category === null) {
                    $isError = true;
                    $errors[] = ['category' => "Category `$categoryId` does not exist"];
                    continue;
                }

                $irregularExpense = new IrregularExpense(
                    $irregularExpenseData['id'] ?? null,
                    $irregularExpenseData['name'] ?? '',
                    $irregularExpenseData['cost'] ?? -1,
                    $category,
                    $irregularExpenseData['position'] ?? -1,
                    $irregularExpenseData['isWish'] ?? false,
                    $irregularExpenseData['plannedYear'] ?? null,
                );
                $irregularExpenses[] = $irregularExpense;

                $errors[] = [];
            } catch (ExpenseNotValidException $exception) {
                $isError = true;
                $errors[] = json_decode($exception->getMessage(), true);
            }
        }

        if ($isError) {
            throw new RequestNotValidException(json_encode($errors));
        }

        return $irregularExpenses;
    }
}
