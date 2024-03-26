<?php

declare(strict_types=1);

namespace App\Tests\Expense\Unit\Domain;

use App\Category\Domain\CategoryNotValidException;
use App\Expense\Domain\AddCurrentExpenseService;
use App\Expense\Domain\CurrentExpenseRepositoryInterface;
use App\Expense\Domain\ExpenseNotValidException;
use App\Tests\Expense\Stub\CurrentExpenseStub;
use PHPUnit\Framework\TestCase;

class AddCurrentExpenseServiceTest extends TestCase
{
    private readonly CurrentExpenseRepositoryInterface $currentExpenseRepository;
    private readonly AddCurrentExpenseService $service;

    protected function setUp(): void
    {
        $this->currentExpenseRepository = $this->createMock(CurrentExpenseRepositoryInterface::class);
        $this->service = new AddCurrentExpenseService(
            $this->currentExpenseRepository,
        );
    }

    /**
     * @throws CategoryNotValidException
     * @throws ExpenseNotValidException
     *
     * @dataProvider executeValidationsDataProvider
     */
    public function testExecuteValidations(array $currentExpenseData, string $exceptedException, string $exceptionMessage): void
    {
        $currentExpense = CurrentExpenseStub::createFromArrayData($currentExpenseData);

        self::expectException($exceptedException);
        self::expectExceptionMessage($exceptionMessage);

        $this->service->execute($currentExpense);
    }

    public static function executeValidationsDataProvider(): array
    {
        return [
            'empty people' => [
                'currentExpenseData' => [
                    'id' => null,
                    'name' => 'Pamiątka z wakacji',
                    'people' => [],
                    'cost' => 30,
                    'category' => '1',
                    'isWish' => true,
                ],
                'exceptedException' => ExpenseNotValidException::class,
                'exceptionMessage' => 'The current expense must have at least one person assigned to it',
            ],
        ];
    }
}
