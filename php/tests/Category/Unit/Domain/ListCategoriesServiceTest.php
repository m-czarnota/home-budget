<?php

namespace App\Tests\Category\Unit\Domain;

use App\Category\Domain\CategoryRepositoryInterface;
use App\Category\Domain\ListCategoriesService;
use App\Tests\Category\Stub\CategoryStub;
use PHPUnit\Framework\TestCase;

class ListCategoriesServiceTest extends TestCase
{
    private readonly CategoryRepositoryInterface $categoryRepository;
    private readonly ListCategoriesService $service;

    protected function setUp(): void
    {
        $this->categoryRepository = $this->createMock(CategoryRepositoryInterface::class);
        $this->service = new ListCategoriesService(
            $this->categoryRepository,
        );
    }

    /**
     * @dataProvider executeDataProvider
     */
    public function testExecute(array $dataInDb): void
    {
        $categoriesInDb = CategoryStub::createMultipleFromArrayData($dataInDb);
        $this->categoryRepository
            ->method('findList')
            ->willReturn($categoriesInDb);

        $categories = $this->service->execute();

        self::assertCount(count($categoriesInDb), $categories);

        foreach ($categories as $categoryIndex => $category) {
            $categoryDataInDb = $categoriesInDb[$categoryIndex];

            self::assertEquals($categoryDataInDb->id, $category->id);
            self::assertEquals($categoryDataInDb->getPosition(), $category->getPosition());
            self::assertEquals($categoryDataInDb->isDeleted(), $category->isDeleted());

            $subCategories = $category->getSubCategories();
            $subCategoriesInDb = $categoryDataInDb->getSubCategories();
            self::assertCount(count($subCategoriesInDb), $subCategories);

            foreach ($subCategories as $subCategoryIndex => $subCategory) {
                $subCategoryData = $subCategoriesInDb[$subCategoryIndex];

                self::assertEquals($subCategoryData->id, $subCategory->id);
                self::assertEquals($subCategoryData->getPosition(), $subCategory->getPosition());
                self::assertEquals($subCategoryData->isDeleted(), $subCategory->isDeleted());
                self::assertCount(count($subCategoryData->getSubCategories()), $subCategory->getSubCategories());
            }
        }
    }

    public static function executeDataProvider(): array
    {
        return [
            'no categories' => [
                'dataInDb' => [],
            ],
            'categories with subcategories' => [
                'dataInDb' => [
                    [
                        'id' => '1',
                        'name' => 'Category 1',
                        'position' => 0,
                        'subCategories' => [],
                    ],
                    [
                        'id' => '2',
                        'name' => 'Category 2',
                        'position' => 1,
                        'subCategories' => [
                            [
                                'id' => '2.1',
                                'name' => 'Category 2.1',
                                'position' => 0,
                            ],
                            [
                                'id' => '2.2',
                                'name' => 'Category 2.2',
                                'position' => 0,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
