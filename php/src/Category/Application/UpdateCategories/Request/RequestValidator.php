<?php

declare(strict_types=1);

namespace App\Category\Application\UpdateCategories\Request;

use App\Category\Application\UpdateCategories\ResponseErrorDto;
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
    public function execute(): ?ResponseErrorDto
    {
        $request = $this->requestStack->getCurrentRequest();
        $data = json_decode($request->getContent(), true);

        $hasPlainValueInsteadOfArray = !empty(array_filter($data, fn (mixed $item) => !is_array($item)));
        if ($hasPlainValueInsteadOfArray) {
            throw new RequestNotValidException('Request can contains only categories without plain values');
        }

        $isError = false;
        $responseErrors = [];

        foreach ($data as $categoryData) {
            $subCategoryErrors = [];

            foreach ($categoryData['subCategories'] ?? [] as $subCategoryData) {
                $requestSubCategoryErrorInfoDto = $this->validateCategoryData($subCategoryData);
                $subCategoryErrors[] = $requestSubCategoryErrorInfoDto;

                if ($requestSubCategoryErrorInfoDto->hasError) {
                    $isError = true;
                }
            }

            $requestCategoryErrorInfoDto = $this->validateCategoryData($categoryData);
            $requestCategoryErrorInfoDto->subCategories = $subCategoryErrors;

            $responseErrors[] = $requestCategoryErrorInfoDto;
            if ($requestCategoryErrorInfoDto->hasError) {
                $isError = true;
            }
        }

        return $isError ? new ResponseErrorDto($responseErrors) : null;
    }

    private function validateCategoryData(array $categoryData): RequestCategoryErrorInfoDto
    {
        $nameError = isset($categoryData['name']) ? null : 'Missing `name` parameter';
        $positionError = isset($categoryData['position']) ? null : 'Missing `position` parameter';

        return new RequestCategoryErrorInfoDto(
            $nameError || $positionError,
            $nameError,
            $positionError,
        );
    }
}
