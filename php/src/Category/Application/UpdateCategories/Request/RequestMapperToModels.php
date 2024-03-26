<?php

declare(strict_types=1);

namespace App\Category\Application\UpdateCategories\Request;

use App\Category\Domain\Category;
use App\Category\Domain\CategoryNotValidException;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\RequestStack;

readonly class RequestMapperToModels
{
    public function __construct(
        private RequestStack $requestStack,
    ) {
    }

    /**
     * @return array<int, Category>
     *
     * @throws RequestNotValidException
     */
    public function execute(): array
    {
        $request = $this->requestStack->getCurrentRequest();
        $data = json_decode($request->getContent(), true);

        $categoryModels = [];
        $isError = false;
        $requestErrorStates = [];

        foreach ($data as $categoryIndex => $categoryData) {
            $categoryErrorDto = new RequestCategoryErrorInfoDto();
            $requestErrorStates[$categoryIndex] = $categoryErrorDto;

            $subCategories = [];
            foreach ($categoryData['subCategories'] ?? [] as $subCategoryData) {
                $subCategoryErrorDto = new RequestCategoryErrorInfoDto();
                $categoryErrorDto->subCategories[] = $subCategoryErrorDto;

                try {
                    $subCategories[] = new Category(
                        $subCategoryData['id'] ?? null,
                        $subCategoryData['name'],
                        $subCategoryData['position'],
                        new DateTimeImmutable($subCategoryData['lastModified'] ?? 'now'),
                    );
                } catch (CategoryNotValidException $e) {
                    $isError = true;
                    $errors = json_decode($e->getMessage(), true);

                    $categoryErrorDto->hasError = true;
                    $subCategoryErrorDto->hasError = true;
                    $subCategoryErrorDto->name = $errors['name'] ?? null;
                    $subCategoryErrorDto->position = $errors['position'] ?? null;
                }
            }

            try {
                $category = new Category(
                    $categoryData['id'] ?? null,
                    $categoryData['name'],
                    $categoryData['position'],
                    new DateTimeImmutable($categoryData['lastModified'] ?? 'now'),
                );
                $categoryModels[] = $category;

                foreach ($subCategories as $subCategory) {
                    $category->addSubCategory($subCategory);
                }
            } catch (CategoryNotValidException $e) {
                $isError = true;
                $errors = json_decode($e->getMessage(), true);

                $categoryErrorDto->hasError = true;
                $categoryErrorDto->name = $errors['name'] ?? null;
                $categoryErrorDto->position = $errors['position'] ?? null;
            }
        }

        if ($isError) {
            throw new RequestNotValidException(json_encode($requestErrorStates));
        }

        return $categoryModels;
    }
}
