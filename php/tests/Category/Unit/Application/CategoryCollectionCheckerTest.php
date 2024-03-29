<?php

declare(strict_types=1);

namespace App\Tests\Category\Unit\Application;

use App\Category\Application\UpdateCategories\CategoryConnectionChecker;
use App\Category\Domain\CategoryRepositoryInterface;
use App\Expense\Domain\CurrentExpenseRepositoryInterface;
use App\Expense\Domain\IrregularExpenseRepositoryInterface;
use App\Tests\Category\Stub\CategoryStub;
use PHPUnit\Framework\TestCase;

class CategoryCollectionCheckerTest extends TestCase
{
    private readonly CategoryRepositoryInterface $categoryRepository;
    private readonly CurrentExpenseRepositoryInterface $currentExpenseRepository;
    private readonly IrregularExpenseRepositoryInterface $irregularExpenseRepository;
    private readonly CategoryConnectionChecker $service;

    protected function setUp(): void
    {
        $this->categoryRepository = $this->createMock(CategoryRepositoryInterface::class);
        $this->currentExpenseRepository = $this->createMock(CurrentExpenseRepositoryInterface::class);
        $this->irregularExpenseRepository = $this->createMock(IrregularExpenseRepositoryInterface::class);

        $this->service = new CategoryConnectionChecker(
            $this->categoryRepository,
            $this->currentExpenseRepository,
            $this->irregularExpenseRepository,
        );
    }

    /**
     * @dataProvider executeDataProvider
     */
    public function testExecute(
        array $categoriesData,
        array $currentExpenseConnectionsData,
        array $irregularExpenseConnectionsData,
        array $expectedIsDeletableData,
    ): void {
        $categories = CategoryStub::createMultipleFromArrayData($categoriesData);
        $this->categoryRepository
            ->method('findAll')
            ->willReturn($categories);

        $flattedCategories = [];
        foreach ($categories as $category) {
            $flattedCategories[$category->id] = $category;

            foreach ($category->getSubCategories() as $subCategory) {
                $flattedCategories[$subCategory->id] = $subCategory;
            }
        }

        $this->currentExpenseRepository
            ->method('hasCategoryAnyConnection')
            ->willReturnMap(array_map(
                fn (string $categoryId, bool $connection) => [$flattedCategories[$categoryId], $connection],
                array_keys($currentExpenseConnectionsData),
                $currentExpenseConnectionsData
            ));

        $this->irregularExpenseRepository
            ->method('hasCategoryAnyConnection')
            ->willReturnMap(array_map(
                fn (string $categoryId, bool $connection) => [$flattedCategories[$categoryId], $connection],
                array_keys($irregularExpenseConnectionsData),
                $irregularExpenseConnectionsData
            ));

        $collection = $this->service->execute();
        self::assertCount(count($categoriesData), $collection);

        foreach ($collection as $categoryId => $categoryDeletionData) {
            $expectedIsCategoryDeletable = $expectedIsDeletableData[$categoryId];
            self::assertSame($categoryId, $categoryDeletionData->id);
            self::assertSame($expectedIsCategoryDeletable['isDeletable'], $categoryDeletionData->isDeletable());

            foreach ($categoryDeletionData->getSubCategories() as $subCategoryId => $subCategoryDeletionData) {
                $expectedIsSubCategoryDeletable = $expectedIsCategoryDeletable['subCategories'][$subCategoryId];
                self::assertSame($subCategoryId, $subCategoryDeletionData->id);
                self::assertSame($expectedIsSubCategoryDeletable, $subCategoryDeletionData->isDeletable());
            }
        }
    }

    public static function executeDataProvider(): array
    {
        return [
            'all categories are not deletable' => [
                'categoriesData' => [
                    [
                        'id' => 'category-1',
                        'name' => 'Category 1',
                        'position' => 0,
                        'subCategories' => [
                            [
                                'id' => 'subcategory-1.1',
                                'name' => 'Subcategory 1.1',
                                'position' => 0,
                            ],
                            [
                                'id' => 'subcategory-1.2',
                                'name' => 'Subcategory 1.2',
                                'position' => 1,
                            ],
                        ],
                    ],
                    [
                        'id' => 'category-2',
                        'name' => 'Category 2',
                        'position' => 1,
                    ],
                ],
                'currentExpenseConnectionsData' => [
                    'category-1' => false,
                    'subcategory-1.1' => true,
                    'subcategory-1.2' => false,
                    'category-2' => true,
                ],
                'irregularExpenseConnectionsData' => [
                    'category-1' => false,
                    'subcategory-1.1' => false,
                    'subcategory-1.2' => true,
                    'category-2' => false,
                ],
                'expectedIsDeletableData' => [
                    'category-1' => [
                        'isDeletable' => false,
                        'subCategories' => [
                            'subcategory-1.1' => false,
                            'subcategory-1.2' => false,
                        ],
                    ],
                    'category-2' => [
                        'isDeletable' => false,
                    ],
                ],
            ],
        ];
    }
}
