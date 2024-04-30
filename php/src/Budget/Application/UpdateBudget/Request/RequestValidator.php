<?php

namespace App\Budget\Application\UpdateBudget\Request;

use App\Budget\Application\UpdateBudget\Response\BadRequestResponse;
use Symfony\Component\HttpFoundation\RequestStack;

readonly class RequestValidator
{
    public function __construct(
        private RequestStack $requestStack,
    ) {
    }

    public function execute(): ?BadRequestResponse
    {
        $request = $this->requestStack->getCurrentRequest();
        $data = json_decode($request->getContent(), true);

        $isMonth = isset($data['month']);

        $isBudgetEntriesError = false;
        $budgetEntriesErrors = [];

        foreach ($data as $budgetEntryData) {
            $budgetEntryErrorInfoDto = $this->validateBudgetEntryData($budgetEntryData);
            $budgetEntriesErrors[] = $budgetEntryErrorInfoDto;

            if ($budgetEntryErrorInfoDto->hasError) {
                $isBudgetEntriesError = true;
            }
        }

        if (!$isMonth || $isBudgetEntriesError) {
            return new BadRequestResponse(
                $isMonth ? '' : 'Missing `month` parameter',
                $budgetEntriesErrors,
            );
        }

        return null;
    }

    private function validateBudgetEntryData(array $budgetEntryData): RequestBudgetEntryErrorInfoDto
    {
        $idError = isset($budgetEntryData['id']) ? null : 'Missing `id` parameter';
        $categoryError = isset($budgetEntryData['category']) ? null : 'Missing `category` parameter';
        $costError = isset($budgetEntryData['cost']) ? null : 'Missing `cost` parameter';
        $plannedMonthError = isset($budgetEntryData['plannedMoth']) ? null : 'Missing `plannedMonth` parameter';

        $isError = $idError || $categoryError || $costError || $plannedMonthError;

        return new RequestBudgetEntryErrorInfoDto(
            $isError,
            $idError,
            $categoryError,
            $costError,
            $plannedMonthError,
        );
    }
}