<?php

namespace App\Tests\Category\Unit\Domain;

use App\Category\Domain\Category;
use App\Category\Domain\CategoryNotValidException;
use App\Category\Domain\SubCategoryNotBelongToCategoryException;
use App\Tests\Category\Stub\CategoryStub;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    /**
     * @throws CategoryNotValidException
     *
     * @dataProvider objectCreateValidationDataProvider
     */
    public function testObjectCreateValidation(?string $id, string $name, int $position, ?string $lastModified, string $exceptionMessage): void
    {
        self::expectException(CategoryNotValidException::class);
        self::expectExceptionMessage($exceptionMessage);

        new Category(
            $id,
            $name,
            $position,
            $lastModified === null ? null : new DateTimeImmutable(),
        );
    }

    public static function objectCreateValidationDataProvider(): array
    {
        return [
            'empty name and position negative' => [
                'id' => null,
                'name' => '',
                'position' => -1,
                'lastModified' => null,
                'exceptionMessage' => json_encode([
                    'name' => 'Name cannot be empty',
                    'position' => 'Position cannot be negative',
                ]),
            ],
            'too long name' => [
                'id' => '1',
                'name' => 'too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name',
                'position' => 0,
                'lastModified' => '2023-04-12 12:51:23',
                'exceptionMessage' => json_encode([
                    'name' => 'Name cannot be longer than 255 characters',
                ]),
            ],
        ];
    }

    /**
     * @dataProvider updateDataProvider
     *
     * @throws SubCategoryNotBelongToCategoryException
     */
    public function testUpdate(array $categoryDataToUpdate, array $existingCategoryData): void
    {
        $categoryToUpdate = CategoryStub::createFromArrayData($categoryDataToUpdate);
        $existingCategory = CategoryStub::createFromArrayData($existingCategoryData);

        $existingCategory->update($categoryToUpdate);
        self::assertEquals($existingCategoryData['id'], $existingCategory->id);
        self::assertEquals($categoryDataToUpdate['name'], $existingCategory->getName());
        self::assertEquals($categoryDataToUpdate['position'], $existingCategory->getPosition());

        $existingSubCategories = array_values($existingCategory->getSubCategories());
        self::assertCount(count($categoryDataToUpdate['subCategories']), $existingSubCategories);

        foreach ($categoryDataToUpdate['subCategories'] as $index => $subCategoryData) {
            $existingSubCategory = $existingSubCategories[$index];

            self::assertEquals($subCategoryData['name'], $existingSubCategory->getName());
            self::assertEquals($subCategoryData['position'], $existingSubCategory->getPosition());
            self::assertCount(0, $existingSubCategory->getSubCategories());
        }
    }

    public static function updateDataProvider(): array
    {
        return [
            'update category properties' => [
                'categoryDataToUpdate' => [
                    'id' => '1',
                    'name' => 'Category 1 changed',
                    'position' => 23,
                    'subCategories' => [],
                ],
                'existingCategoryData' => [
                    'id' => '1',
                    'name' => 'Category 1',
                    'position' => 0,
                    'subCategories' => [],
                ],
            ],
            'update sub subcategories' => [
                'categoryDataToUpdate' => [
                    'id' => '1',
                    'name' => 'Category 1 changed',
                    'position' => 23,
                    'subCategories' => [
                        [
                            'id' => '1.1',
                            'name' => 'Sub Category 1 changed',
                            'position' => 2,
                        ],
                    ],
                ],
                'existingCategoryData' => [
                    'id' => '1',
                    'name' => 'Category 1',
                    'position' => 0,
                    'subCategories' => [
                        [
                            'id' => '1.1',
                            'name' => 'Sub Category 1',
                            'position' => 0,
                        ],
                    ],
                ],
            ],
            'adding and remove some sub categories' => [
                'categoryDataToUpdate' => [
                    'id' => '1',
                    'name' => 'Category 1 changed',
                    'position' => 23,
                    'subCategories' => [
                        [
                            'id' => '1.2',
                            'name' => 'Sub Category 2 changed',
                            'position' => 0,
                        ],
                        [
                            'name' => 'Sub Category 3',
                            'position' => 1,
                        ],
                    ],
                ],
                'existingCategoryData' => [
                    'id' => '1',
                    'name' => 'Category 1',
                    'position' => 0,
                    'subCategories' => [
                        [
                            'id' => '1.1',
                            'name' => 'Sub Category 1',
                            'position' => 0,
                        ],
                        [
                            'id' => '1.2',
                            'name' => 'Sub Category 2',
                            'position' => 1,
                        ],
                    ],
                ],
            ],
        ];
    }

    public function testHasSubCategory(): void
    {
        $category = new Category(
            null,
            'Category',
            0,
        );

        $subCategory = new Category(
            '1',
            'Sub category 1',
            0,
        );
        $category->addSubCategory($subCategory);

        $nonExistedSubCategory = new Category(
            null,
            'Sub Category 2',
            1,
        );

        self::assertTrue($category->hasSubCategory($subCategory));
        self::assertFalse($category->hasSubCategory($nonExistedSubCategory));
    }

    public function testAddSubcategory(): void
    {
        $category = new Category(
            null,
            'Category',
            0,
        );

        $subCategory = new Category(
            '1',
            'Sub category 1',
            0,
        );
        $category->addSubCategory($subCategory);

        self::assertCount(1, $category->getSubCategories());
        self::assertEquals($subCategory->id, $category->findSubCategory($subCategory->id)?->id);
    }

    public function testRemoveSubCategory(): void
    {
        $category = new Category(
            null,
            'Category',
            0,
        );
        $subCategory = new Category(
            '1',
            'Sub category 1',
            0,
        );
        $category->addSubCategory($subCategory);

        $category->removeSubCategory($subCategory);
        self::assertCount(0, $category->getSubCategories());
    }
}
