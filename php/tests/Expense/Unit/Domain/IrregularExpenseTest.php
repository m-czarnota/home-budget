<?php

namespace App\Tests\Expense\Unit\Domain;

use App\Category\Domain\CategoryNotValidException;
use App\Expense\Domain\ExpenseNotValidException;
use App\Expense\Domain\IrregularExpense;
use App\Tests\Category\Stub\CategoryStub;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class IrregularExpenseTest extends TestCase
{
    public function testCreateObject(): void
    {
        $irregularExpense = new IrregularExpense(
            null,
            'Wyjazd do Sztokholmu',
            5200,
            CategoryStub::createExampleCategory('1'),
            0,
            true,
            2024,
        );
        self::assertInstanceOf(IrregularExpense::class, $irregularExpense);
    }

    /**
     * @throws CategoryNotValidException
     *
     * @dataProvider createObjectValidationsDataProvider
     */
    public function testCreateObjectValidations(array $irregularExpenseData, string $exceptionMessage): void
    {
        self::expectException(ExpenseNotValidException::class);
        self::expectExceptionMessage($exceptionMessage);

        new IrregularExpense(
            $irregularExpenseData['id'] ?? null,
            $irregularExpenseData['name'],
            $irregularExpenseData['cost'],
            CategoryStub::createExampleCategory($irregularExpenseData['categoryId'] ?? null),
            $irregularExpenseData['position'],
            $irregularExpenseData['isWish'],
            $irregularExpenseData['plannedYear'],
        );
    }

    public function createObjectValidationsDataProvider(): array
    {
        $currentDate = new DateTimeImmutable();
        $currentYear = intval($currentDate->format('Y'));

        return [
            'empty name, negative cost, earlier planned year than current' => [
                'irregularExpenseData' => [
                    'name' => '',
                    'cost' => -2,
                    'position' => 0,
                    'isWish' => false,
                    'plannedYear' => $currentYear - 1,
                ],
                'exceptionMessage' => json_encode([
                    'name' => 'Name cannot be empty',
                    'cost' => 'Cost cannot be negative',
                    'plannedYear' => "Planned year cannot be earlier than current year $currentYear",
                ]),
            ],
            'too long name, cost is 0, position negative' => [
                'irregularExpenseData' => [
                    'name' => 'too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name too long name',
                    'cost' => 0,
                    'position' => -4,
                    'isWish' => false,
                    'plannedYear' => $currentYear,
                ],
                'exceptionMessage' => json_encode([
                    'name' => 'Name cannot be longer than 255 characters',
                    'cost' => 'Cost cannot be 0',
                    'position' => 'Position cannot be negative',
                ]),
            ],
        ];
    }
}
