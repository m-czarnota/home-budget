<?php

declare(strict_types=1);

namespace App\Tests\Expense\Unit\Application\UpdateIrregularExpenses;

use App\Expense\Application\UpdateIrregularExpenses\RequestValidator;
use DateTime;
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
            $expectedNameError = $exceptedError['name'] ?? null;
            if ($expectedNameError) {
                self::assertEquals($expectedNameError, $error->name);
            }

            $exceptedCostError = $exceptedError['cost'] ?? null;
            if ($exceptedCostError) {
                self::assertEquals($exceptedCostError, $error->cost);
            }

            $exceptedCategoryError = $exceptedError['category'] ?? null;
            if ($exceptedCategoryError) {
                self::assertEquals($exceptedCategoryError, $error->category);
            }

            $expectedPositionError = $exceptedError['position'] ?? null;
            if ($expectedPositionError) {
                self::assertEquals($expectedPositionError, $error->position);
            }

            $expectedPlannedYearError = $exceptedError['plannedYear'] ?? null;
            if ($expectedPlannedYearError) {
                self::assertEquals($expectedPlannedYearError, $error->plannedYear);
            }
        }
    }

    public static function executeDataProvider(): array
    {
        $date = new DateTime();
        $currentYear = intval($date->format('Y'));

        return [
            'no errors' => [
                'requestContent' => json_encode([
                    [
                        'name' => 'Wakacje w Budapeszcie',
                        'cost' => '5200',
                        'category' => '1',
                        'position' => 0,
                        'plannedYear' => $currentYear,
                    ],
                    [
                        'name' => 'Prezenty na święta',
                        'cost' => '1000',
                        'category' => '2',
                        'position' => 1,
                        'plannedYear' => $currentYear,
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
                        'plannedYear' => $currentYear,
                    ],
                    [],
                    [
                        'name' => 'Prezenty na święta',
                        'cost' => '1000',
                        'category' => '2',
                        'position' => 1,
                        'plannedYear' => $currentYear,
                    ],
                ]),
                'exceptedErrors' => [
                    [],
                    [
                        'name' => 'Missing `name` parameter',
                        'cost' => 'Missing `cost` parameter',
                        'category' => 'Missing `category` parameter',
                        'position' => 'Missing `position` parameter',
                        'plannedYear' => 'Missing `plannedYear` parameter',
                    ],
                    [],
                ],
            ],
        ];
    }
}
