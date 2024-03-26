<?php

declare(strict_types=1);

namespace App\Tests\Category\Stub;

use App\Category\Domain\Category;
use App\Category\Domain\CategoryNotValidException;
use DateTimeImmutable;

class CategoryStub
{
    private static int $position = 0;

    /**
     * @return array<int, Category>
     */
    public static function createMultipleFromArrayData(array $categoriesData): array
    {
        $categories = [];

        foreach ($categoriesData as $categoryData) {
            $category = self::createFromArrayData($categoryData);
            $categories[$category->id] = $category;
        }

        return $categories;
    }

    public static function createFromArrayData(array $categoryData): Category
    {
        $category = new Category(
            $categoryData['id'] ?? null,
            $categoryData['name'],
            $categoryData['position'],
            new DateTimeImmutable($categoryData['lastModified'] ?? 'now'),
        );

        foreach ($categoryData['subCategories'] ?? [] as $subCategoryData) {
            $category->addSubCategory(new Category(
                $subCategoryData['id'] ?? null,
                $subCategoryData['name'],
                $subCategoryData['position'],
                new DateTimeImmutable($subCategoryData['lastModified'] ?? 'now'),
            ));
        }

        return $category;
    }

    /**
     * @throws CategoryNotValidException
     */
    public static function createExampleCategory(
        ?string $id = null,
        string $name = 'Example Category',
        array $subCategoriesData = [],
        ?int $position = null,
    ): Category {
        $category = new Category(
            $id,
            $name,
            $position ?? self::$position++,
        );

        foreach ($subCategoriesData as $subCategoryData) {
            $category->addSubCategory(self::createExampleCategory(
                $subCategoryData['id'],
                $subCategoryData['name'],
            ));
        }

        return $category;
    }
}
