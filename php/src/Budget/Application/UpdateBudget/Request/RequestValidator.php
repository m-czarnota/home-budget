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

        foreach ($data['entries'] as $budgetEntryData) {
            $budgetEntryErrorInfoDto = $this->validateBudgetEntryData($budgetEntryData);
            $budgetEntriesErrors[] = $budgetEntryErrorInfoDto;

            foreach ($budgetEntryData['subEntries'] as $subEntryData) {
                $budgetSubEntryErrorInfoDto = $this->validateBudgetEntryData($subEntryData);
                $budgetEntryErrorInfoDto->subEntries[] = $budgetSubEntryErrorInfoDto;

                if ($budgetSubEntryErrorInfoDto->hasError) {
                    $isBudgetEntriesError = true;
                }
            }

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
        $categoryError = isset($budgetEntryData['categoryId']) ? null : 'Missing `categoryId` parameter';
        $costError = isset($budgetEntryData['cost']) ? null : 'Missing `cost` parameter';

        $isError = $idError || $categoryError || $costError;

        return new RequestBudgetEntryErrorInfoDto(
            $isError,
            $idError,
            $categoryError,
            $costError,
        );
    }
}