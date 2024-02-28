<?php

namespace App\Tests\Expense\Unit\Application\UpdateIrregularExpenses;

use App\Expense\Application\UpdateIrregularExpenses\RequestValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestValidatorTest extends TestCase
{
    private readonly RequestStack $requestStack;
    private RequestValidator $service;

    protected function setUp(): void
    {
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->service = new RequestValidator(
            $this->requestStack,
        );
    }

    /**
     * @dataProvider executeDataProvider
     */
    public function testExecute(string $requestContent, ?array $exceptedErrors): void
    {
        $request = $this->createMock(Request::class);
        $request
            ->method('getContent')
            ->willReturn($requestContent);

        $this->requestStack
            ->method('getCurrentRequest')
            ->willReturn($request);

        $responseError = $this->service->execute();

        if ($exceptedErrors === null) {
            self::assertNull($responseError);

            return;
        }

        $errors = $responseError->errors;
        self::assertCount(count($exceptedErrors), $errors);

        foreach ($errors as $errorIndex => $error) {
            $exceptedError = $error[$errorIndex] ?? null;
            if (empty($exceptedError)) {
                continue;
            }

            $expectedNameError = $exceptedError['name'] ?? null;
            if ($expectedNameError) {
                self::assertEquals($expectedNameError, $error['name']);
            }

            $exceptedCostError = $exceptedError['cost'] ?? null;
            if ($exceptedCostError) {
                self::assertEquals($exceptedCostError, $error['cost']);
            }

            $exceptedCategoryError = $exceptedError['category'] ?? null;
            if ($exceptedCategoryError) {
                self::assertEquals($exceptedCategoryError, $error['category']);
            }

            $expectedPositionError = $exceptedError['position'] ?? null;
            if ($expectedPositionError) {
                self::assertEquals($expectedPositionError, $error['position']);
            }
        }
    }

    public static function executeDataProvider(): array
    {
        return [
            'no errors' => [
                'requestContent' => json_encode([
                    [
                        'name' => 'Wakacje w Budapeszcie',
                        'cost' => '5200',
                        'category' => '1',
                        'position' => 0,
                    ],
                    [
                        'name' => 'Prezenty na święta',
                        'cost' => '1000',
                        'category' => '2',
                        'position' => 1,
                    ],
                ]),
                'exceptedErrors' => null,
            ],
            'empty second expense' => [
                'requestContent' => json_encode([
                    [
                        'name' => 'Wakacje w Budapeszcie',
                        'cost' => '5200',
                        'category' => '1',
                        'position' => 0,
                    ],
                    [],
                    [
                        'name' => 'Prezenty na święta',
                        'cost' => '1000',
                        'category' => '2',
                        'position' => 1,
                    ],
                ]),
                'exceptedErrors' => [
                    [],
                    [
                        'name' => 'Missing `name` parameter',
                        'cost' => 'Missing `cost` parameter',
                        'category' => 'Missing `category` parameter',
                        'position' => 'Missing `position` parameter',
                    ],
                    [],
                ],
            ],
        ];
    }
}
