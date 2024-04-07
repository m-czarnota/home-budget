<?php

declare(strict_types=1);

namespace App\Expense\Application\UpdateIrregularExpenses;

use Symfony\Component\HttpFoundation\RequestStack;

readonly class RequestValidator
{
    public function __construct(
        private RequestStack $requestStack,
    ) {
    }

    /**
     * @throws RequestNotValidException
     */
    public function execute(): ?ResponseError
    {
        $request = $this->requestStack->getCurrentRequest();
        $data = json_decode(trim($request->getContent()), true);

        if (empty($data)) {
            throw new RequestNotValidException('Sent request has not content');
        }

        $isError = false;
        $errors = [];

        foreach ($data as $irregularExpenseData) {
            $requestIrregularExpenseErrorInfoDto = $this->validateIrregularExpenseData($irregularExpenseData);
            $errors[] = $requestIrregularExpenseErrorInfoDto;

            if ($requestIrregularExpenseErrorInfoDto->hasError) {
                $isError = true;
            }
        }

        return $isError ? new ResponseError($errors) : null;
    }

    private function validateIrregularExpenseData(array $irregularExpenseData): RequestIrregularExpenseErrorInfoDto
    {
        $nameError = isset($irregularExpenseData['name']) ? null : 'Missing `name` parameter';
        $positionError = isset($irregularExpenseData['position']) ? null : 'Missing `position` parameter';
        $costError = isset($irregularExpenseData['cost']) ? null : 'Missing `cost` parameter';
        $categoryError = isset($irregularExpenseData['category']) ? null : 'Missing `category` parameter';
        $plannedYearError = isset($irregularExpenseData['plannedYear']) ? null : 'Missing `plannedYear` parameter';

        $isError = $nameError || $positionError || $costError || $categoryError || $plannedYearError;

        return new RequestIrregularExpenseErrorInfoDto(
            $isError,
            $nameError,
            $positionError,
            $costError,
            $plannedYearError,
            $categoryError,
        );
    }
}
