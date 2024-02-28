<?php

namespace App\Tests\Expense\Unit\Domain;

use App\Expense\Domain\CurrentExpense;
use App\Expense\Domain\ExpenseNotValidException;
use App\Tests\Category\Stub\CategoryStub;
use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\TestCase;

class CurrentExpenseTest extends TestCase
{
    /**
     * @throws ExpenseNotValidException
     * @throws Exception
     *
     * @dataProvider createObjectValidationsDataProvider
     */
    public function testCreateObjectValidations(array $currentExpenseData, string $exceptedException, string $exceptionMessage): void
    {
        $dateOfExpense = new DateTimeImmutable($currentExpenseData['dateOfExpense']);
        $category = CategoryStub::createExampleCategory($currentExpenseData['category']);

        self::expectException($exceptedException);
        self::expectExceptionMessage($exceptionMessage);

        new CurrentExpense(
            $currentExpenseData['id'] ?? null,
            $currentExpenseData['name'],
            $currentExpenseData['cost'],
            $category,
            $currentExpenseData['isWish'],
            $dateOfExpense,
        );
    }

    public static function createObjectValidationsDataProvider(): array
    {
        return [
            'empty name, negative cost' => [
                'currentExpenseData' => [
                    'name' => '',
                    'cost' => -2,
                    'category' => '1',
                    'isWish' => false,
                    'dateOfExpense' => '2023-01-20',
                ],
                'exceptedException' => ExpenseNotValidException::class,
                'exceptionMessage' => json_encode([
                    'name' => 'Name cannot be empty',
                    'cost' => 'Cost cannot be negative',
                ]),
            ],
            'too long name, cost equals 0' => [
                'currentExpenseData' => [
                    'name' => 'too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name',
                    'cost' => 0,
                    'category' => '1',
                    'isWish' => true,
                    'dateOfExpense' => '2023-02-19',
                ],
                'exceptedException' => ExpenseNotValidException::class,
                'exceptionMessage' => json_encode([
                    'name' => 'Name cannot be longer than 255 characters',
                    'cost' => 'Cost cannot be 0',
                ]),
            ],
        ];
    }
}
