<?php

declare(strict_types=1);

namespace App\Tests\Expense\Stub;

use App\Category\Domain\CategoryNotValidException;
use App\Expense\Domain\CurrentExpense;
use App\Expense\Domain\ExpenseNotValidException;
use App\Tests\Category\Stub\CategoryStub;
use App\Tests\Person\Stub\PersonStub;
use DateTimeImmutable;
use Exception;

class CurrentExpenseStub
{
    /**
     * @throws CategoryNotValidException
     * @throws ExpenseNotValidException
     * @throws Exception
     */
    public static function createFromArrayData(array $currentExpenseData): CurrentExpense
    {
        $currentExpense = new CurrentExpense(
            $currentExpenseData['id'] ?? null,
            $currentExpenseData['name'],
            $currentExpenseData['cost'],
            CategoryStub::createExampleCategory($currentExpenseData['category']),
            $currentExpenseData['isWish'] ?? false,
            new DateTimeImmutable($currentExpenseData['dateOfExpense'] ?? 'now'),
        );

        foreach ($currentExpenseData['people'] as $person) {
            $currentExpense->addPerson(PersonStub::createExamplePerson($person));
        }

        return $currentExpense;
    }

    /**
     * @throws CategoryNotValidException
     * @throws ExpenseNotValidException
     */
    public static function createExample(?string $id = null): CurrentExpense
    {
        return new CurrentExpense(
            $id,
            'Example expense',
            100,
            CategoryStub::createExampleCategory(),
        );
    }
}
