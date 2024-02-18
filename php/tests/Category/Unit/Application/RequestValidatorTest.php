<?php

namespace App\Tests\Category\Unit\Application;

use App\Category\Application\RequestNotValidException;
use App\Category\Application\RequestValidator;
use App\Category\Application\ResponseErrorDto;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestValidatorTest extends TestCase
{
    private readonly RequestStack $requestStack;
    private readonly RequestValidator $service;

    protected function setUp(): void
    {
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->service = new RequestValidator($this->requestStack);
    }

    /**
     * @dataProvider executeDataProvider
     *
     * @throws RequestNotValidException
     */
    public function testExecute(array $requestData, array $exceptedErrors): void
    {
        $request = $this->createMock(Request::class);
        $request
            ->method('getContent')
            ->willReturn(json_encode($requestData));

        $this->requestStack
            ->method('getCurrentRequest')
            ->willReturn($request);

        $errors = $this->service->execute();
        if (empty($exceptedErrors)) {
            self::assertNull($errors);

            return;
        }

        self::assertInstanceOf(ResponseErrorDto::class, $errors);
        self::assertTrue($errors->isError);

        foreach ($errors->errors as $categoryIndex => $error) {
            $exceptedError = $exceptedErrors[$categoryIndex] ?? null;

            self::assertNotNull($exceptedError);
            self::assertEquals($exceptedError['name'], $error->name);
            self::assertEquals($exceptedError['position'], $error->position);

            foreach ($error->subCategories as $subCategoryIndex => $subCategory) {
                $exceptedSubCategoryError = $exceptedError['subCategories'][$subCategoryIndex] ?? null;

                self::assertNotNull($exceptedSubCategoryError);
                self::assertEquals($exceptedSubCategoryError['name'], $subCategory->name);
                self::assertEquals($exceptedSubCategoryError['position'], $subCategory->position);
                self::assertCount(0, $subCategory->subCategories);
            }
        }
    }

    public function executeDataProvider(): array
    {
        return [
            'no errors' => [
                'requestData' => [
                    [
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
                                'name' => 'Sub category 2.1',
                                'position' => 0,
                            ],
                        ],
                    ],
                ],
                'exceptedErrors' => [],
            ],
            'missing names and positions' => [
                'requestData' => [
                    [
                        'position' => 0,
                        'subCategories' => [],
                    ],
                    [
                        'id' => '2',
                        'name' => 'Category 2',
                        'subCategories' => [
                            [
                                'position' => 0,
                            ],
                            [
                                'name' => 'Sub category 2.2',
                            ],
                        ],
                    ],
                ],
                'exceptedErrors' => [
                    [
                        'name' => 'Missing `name` parameter',
                        'position' => null,
                        'subCategories' => [],
                    ],
                    [
                        'name' => null,
                        'position' => 'Missing `position` parameter',
                        'subCategories' => [
                            [
                                'name' => 'Missing `name` parameter',
                                'position' => null,
                                'subCategories' => [],
                            ],
                            [
                                'name' => null,
                                'position' => 'Missing `position` parameter',
                                'subCategories' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider executeValidationsDataProvider
     *
     * @throws RequestNotValidException
     */
    public function testExecuteValidations(array $requestData, string $exception, string $exceptionMessage): void
    {
        $request = $this->createMock(Request::class);
        $request
            ->method('getContent')
            ->willReturn(json_encode($requestData));

        $this->requestStack
            ->method('getCurrentRequest')
            ->willReturn($request);

        self::expectException($exception);
        self::expectExceptionMessage($exceptionMessage);

        $this->service->execute();
    }

    public static function executeValidationsDataProvider(): array
    {
        return [
            'request has plain fields' => [
                'requestData' => [
                    'plainData' => 'something',
                    [
                        'name' => 'Category',
                        'position' => 0,
                        'subCategories' => [],
                    ],
                ],
                'exception' => RequestNotValidException::class,
                'exceptionMessage' => 'Request can contains only categories without plain values',
            ],
        ];
    }
}
