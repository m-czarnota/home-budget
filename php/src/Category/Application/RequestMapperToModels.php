<?php

namespace App\Category\Application;

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
        $requestErrorStates = [];

        foreach ($data as $categoryIndex => $categoryData) {
            $requestErrorStates[$categoryIndex] = ['subCategories' => []];

            $subCategories = [];
            foreach ($categoryData['subCategories'] ?? [] as $subCategoryData) {
                try {
                    $subCategories[] = new Category(
                        $subCategoryData['id'] ?? null,
                        $subCategoryData['name'],
                        $subCategoryData['position'],
                        new DateTimeImmutable($subCategoryData['lastModified'] ?? 'now'),
                    );

                    $requestErrorStates[$categoryIndex]['subCategories'][] = [];
                } catch (CategoryNotValidException $e) {
                    $requestErrorStates[$categoryIndex]['subCategories'][] = json_decode($e->getMessage(), true);
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
                $errors = json_decode($e->getMessage(), true);
                $requestErrorStates[$categoryIndex] = array_merge($requestErrorStates[$categoryIndex], $errors);
            }
        }

        if (!empty($errors)) {
            throw new RequestNotValidException(json_encode($requestErrorStates));
        }

        return $categoryModels;
    }
}
