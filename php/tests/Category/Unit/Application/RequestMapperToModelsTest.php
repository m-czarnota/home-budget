<?php

namespace App\Tests\Category\Unit\Application;

use App\Category\Application\RequestMapperToModels;
use App\Category\Application\RequestNotValidException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestMapperToModelsTest extends TestCase
{
    private readonly RequestStack $requestStack;
    private readonly RequestMapperToModels $service;

    protected function setUp(): void
    {
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->service = new RequestMapperToModels($this->requestStack);
    }

    /**
     * @throws RequestNotValidException
     *
     * @dataProvider executeDataProvider
     */
    public function testExecute(array $requestData): void
    {
        $request = $this->createMock(Request::class);
        $request
            ->method('getContent')
            ->willReturn(json_encode($requestData));

        $this->requestStack
            ->method('getCurrentRequest')
            ->willReturn($request);

        $categories = $this->service->execute();

        foreach ($categories as $categoryIndex => $category) {
            $categoryData = $requestData[$categoryIndex];

            $categoryDataId = $categoryData['id'] ?? null;
            if ($categoryDataId) {
                self::assertEquals($categoryDataId, $category->id);
            }

            self::assertEquals($categoryData['name'], $category->getName());
            self::assertEquals($categoryData['position'], $category->getPosition());

            $subCategoriesData = $categoryData['subCategories'] ?? [];
            $subCategories = array_values($category->getSubCategories());
            self::assertCount(count($subCategoriesData), $subCategories);

            foreach ($subCategories as $subcategoryIndex => $subCategory) {
                $subCategoryData = $subCategoriesData[$subcategoryIndex];

                $subCategoryDataId = $subCategoryData['id'] ?? null;
                if ($subCategoryDataId) {
                    self::assertEquals($subCategoryDataId, $subCategory->id);
                }

                self::assertEquals($subCategoryData['name'], $subCategory->getName());
                self::assertEquals($subCategoryData['position'], $subCategory->getPosition());
                self::assertCount(0, $subCategory->getSubCategories());
            }
        }
    }

    public static function executeDataProvider(): array
    {
        return [
            'only new categories' => [
                'requestData' => [
                    [
                        'name' => 'Category 1',
                        'position' => 0,
                        'subCategories' => [
                            [
                                'name' => 'Sub category 1.1',
                                'position' => 0,
                            ],
                        ],
                    ],
                    [
                        'name' => 'Category 2',
                        'position' => 1,
                        'subCategories' => [],
                    ],
                ],
            ],
            'only existing categories' => [
                'requestData' => [
                    [
                        'id' => '1',
                        'name' => 'Category 1',
                        'position' => 0,
                        'subCategories' => [
                            [
                                'id' => '1.1',
                                'name' => 'Sub category 1.1',
                                'position' => 0,
                            ],
                        ],
                    ],
                    [
                        'id' => '2',
                        'name' => 'Category 2',
                        'position' => 1,
                        'subCategories' => [],
                    ],
                ],
            ],
            'existing with new categories' => [
                'requestData' => [
                    [
                        'id' => '1',
                        'name' => 'Category 1',
                        'position' => 0,
                        'subCategories' => [
                            [
                                'id' => '1.1',
                                'name' => 'Sub category 1.1',
                                'position' => 0,
                            ],
                            [
                                'name' => 'Sub category 1.2',
                                'position' => 1,
                            ],
                        ],
                    ],
                    [
                        'name' => 'Category 2',
                        'position' => 1,
                        'subCategories' => [
                            [
                                'name' => 'Sub category 2.1',
                                'position' => 0,
                            ],
                            [
                                'name' => 'Sub category 2.2',
                                'position' => 1,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @throws RequestNotValidException
     *
     * @dataProvider executeValidationsDataProvider
     */
    public function testExecuteValidations(array $requestData, string $exceptionMessage): void
    {
        $request = $this->createMock(Request::class);
        $request
            ->method('getContent')
            ->willReturn(json_encode($requestData));

        $this->requestStack
            ->method('getCurrentRequest')
            ->willReturn($request);

        self::expectException(RequestNotValidException::class);
        self::expectExceptionMessage($exceptionMessage);
        $this->service->execute();
    }

    public static function executeValidationsDataProvider(): array
    {
        return [
            'wrong category data' => [
                'requestData' => [
                    [
                        'name' => '',
                        'position' => -1,
                        'subCategories' => [],
                    ],
                    [
                        'id' => '2',
                        'name' => 'too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name',
                        'position' => -2,
                        'subCategories' => [
                            [
                                'name' => 'Sub category 2.1',
                                'position' => -4,
                            ],
                            [
                                'name' => 'Sub category 2.2',
                                'position' => 2,
                            ],
                        ],
                    ],
                ],
                'exceptionMessage' => json_encode([
                    [
                        'subCategories' => [],
                        'name' => 'Name cannot be empty',
                        'position' => 'Position cannot be negative',
                    ],
                    [
                        'subCategories' => [
                            [
                                'position' => 'Position cannot be negative',
                            ],
                            [
                            ],
                        ],
                        'name' => 'Name cannot be longer than 255 characters',
                        'position' => 'Position cannot be negative',
                    ],
                ]),
            ],
        ];
    }
}
