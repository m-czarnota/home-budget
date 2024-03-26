<?php

declare(strict_types=1);

namespace App\Tests\Category\Unit\Domain;

use App\Category\Domain\Category;
use App\Category\Domain\CategoryDeletionInfoCollection;
use App\Category\Domain\CategoryRepositoryInterface;
use App\Category\Domain\UpdateCategoriesService;
use App\Tests\Category\Stub\CategoryStub;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UpdateCategoriesServiceTest extends TestCase
{
    private readonly CategoryRepositoryInterface $categoryRepository;
    private readonly UpdateCategoriesService $service;

    protected function setUp(): void
    {
        $this->categoryRepository = $this->createMock(CategoryRepositoryInterface::class);
        $this->service = new UpdateCategoriesService(
            $this->categoryRepository,
        );
    }

    /**
     * @dataProvider executeDataProvider
     */
    public function testExecute(array $categoriesToUpdateData, array $existingCategoriesData): void
    {
        $existingCategories = CategoryStub::createMultipleFromArrayData($existingCategoriesData);
        $this->categoryRepository
            ->method('findOneById')
            ->willReturnMap(array_map(
                fn (Category $category) => [$category->id, $category],
                $existingCategories
            ));

        $categories = CategoryStub::createMultipleFromArrayData($categoriesToUpdateData);
        $updatedCategories = $this->service->execute(new CategoryDeletionInfoCollection(), ...$categories);

        // checking if updated data state corresponds to received data as $categoriesToUpdateData
        self::assertCount(count($categoriesToUpdateData), $updatedCategories);

        $categoryIter = 0;
        foreach ($updatedCategories as $categoryIndex => $updatedCategory) {
            $categoryData = $categoriesToUpdateData[$categoryIter];
            self::assertEquals($categoryData['name'], $updatedCategory->getName());
            self::assertEquals($categoryData['position'], $updatedCategory->getPosition());
            self::assertFalse($updatedCategory->isDeleted());
            self::assertCount(count($categoryData['subCategories']), $updatedCategory->getSubCategories());

            $subCategoryIter = 0;
            foreach ($updatedCategory->getSubCategories() as $subCategoryIndex => $updatedSubCategory) {
                $subCategoryData = $categoryData['subCategories'][$subCategoryIter];
                self::assertEquals($subCategoryData['name'], $updatedSubCategory->getName());
                self::assertEquals($subCategoryData['position'], $updatedSubCategory->getPosition());
                self::assertFalse($updatedSubCategory->isDeleted());
                self::assertCount(0, $updatedSubCategory->getSubCategories());

                ++$subCategoryIter;
            }

            ++$categoryIter;
        }
    }

    public static function executeDataProvider(): array
    {
        return [
            'creating first categories' => [
                'categoriesToUpdateData' => [
                    [
                        'name' => 'Category 1',
                        'position' => 0,
                        'subCategories' => [
                            [
                                'name' => 'Sub Category 1',
                                'position' => 0,
                            ],
                            [
                                'name' => 'Sub Category 2',
                                'position' => 1,
                            ],
                        ],
                    ],
                    [
                        'name' => 'Category 2',
                        'position' => 1,
                        'subCategories' => [],
                    ],
                ],
                'existingCategoriesData' => [],
            ],
            'adding sub categories' => [
                'categoriesToUpdateData' => [
                    [
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
                            [
                                'name' => 'Sub Category 3',
                                'position' => 2,
                            ],
                        ],
                    ],
                    [
                        'id' => '2',
                        'name' => 'Category 2',
                        'position' => 1,
                        'subCategories' => [
                            [
                                'name' => 'Sub Category 2.2',
                                'position' => 0,
                            ],
                        ],
                    ],
                ],
                'existingCategoriesData' => [
                    [
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
                    [
                        'id' => '1.2',
                        'name' => 'Category 2',
                        'position' => 1,
                        'subCategories' => [],
                    ],
                ],
            ],
            'removing some categories and sub categories' => [
                'categoriesToUpdateData' => [
                    [
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
                                'id' => '1.3',
                                'name' => 'Sub Category 3',
                                'position' => 1,
                            ],
                            [
                                'name' => 'Sub Category 4',
                                'position' => 2,
                            ],
                        ],
                    ],
                    [
                        'name' => 'Category 3',
                        'position' => 1,
                        'subCategories' => [
                            [
                                'name' => 'Sub Category 3.1',
                                'position' => 0,
                            ],
                        ],
                    ],
                ],
                'existingCategoriesData' => [
                    [
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
                            [
                                'id' => '1.3',
                                'name' => 'Sub Category 3',
                                'position' => 2,
                            ],
                        ],
                    ],
                    [
                        'id' => '2',
                        'name' => 'Category 2',
                        'position' => 1,
                        'subCategories' => [
                            [
                                'id' => '2.1',
                                'name' => 'Sub Category 2.1',
                                'position' => 0,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
