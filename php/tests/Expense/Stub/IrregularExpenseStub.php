<?php

namespace App\Tests\Expense\Stub;

use App\Category\Domain\CategoryNotValidException;
use App\Expense\Domain\ExpenseNotValidException;
use App\Expense\Domain\IrregularExpense;
use App\Tests\Category\Stub\CategoryStub;
use DateTimeImmutable;
use Exception;

class ExpenseStub
{
    /**
     * @param array $peopleData
     * @return array<int, IrregularExpense>
     * @throws ExpenseNotValidException
     */
    public static function createMultipleFromArrayData(array $peopleData): array
    {
        return array_map(fn (array $personData) => self::createFromArrayData($personData), $peopleData);
    }

    /**
     * @throws ExpenseNotValidException
     * @throws Exception
     */
    public static function createFromArrayData(array $irregularExpenseData): IrregularExpense
    {
        return new IrregularExpense(
            $irregularExpenseData['id'] ?? null,
            $irregularExpenseData['name'],
            $irregularExpenseData['cost'],
            $irregularExpenseData['category'],
            $irregularExpenseData['position'],
            $irregularExpenseData['isWish'],
            $irregularExpenseData['plannedYear'],
        );
    }

    /**
     * @throws CategoryNotValidException
     * @throws ExpenseNotValidException
     */
    public static function createExample(?string $id = null, ?string $categoryId = null): IrregularExpense
    {
        $currentDate = new DateTimeImmutable();
        $currentYear = intval($currentDate->format('Y'));

        return new IrregularExpense(
            $id,
            'Example Irregular Expense',
            1000,
            CategoryStub::createExampleCategory($categoryId),
            0,
            false,
            $currentYear,
        );
    }
}