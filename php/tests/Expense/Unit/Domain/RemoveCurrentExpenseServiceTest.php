<?php

namespace App\Tests\Expense\Unit\Domain;

use App\Category\Domain\CategoryNotValidException;
use App\Expense\Domain\CurrentExpenseRepositoryInterface;
use App\Expense\Domain\ExpenseNotFoundException;
use App\Expense\Domain\ExpenseNotValidException;
use App\Expense\Domain\RemoveCurrentExpenseService;
use App\Tests\Expense\Stub\CurrentExpenseStub;
use PHPUnit\Framework\TestCase;

class RemoveCurrentExpenseServiceTest extends TestCase
{
    private readonly CurrentExpenseRepositoryInterface $currentExpenseRepository;
    private readonly RemoveCurrentExpenseService $service;

    protected function setUp(): void
    {
        $this->currentExpenseRepository = $this->createMock(CurrentExpenseRepositoryInterface::class);
        $this->service = new RemoveCurrentExpenseService(
            $this->currentExpenseRepository,
        );
    }

    /**
     * @throws ExpenseNotFoundException
     * @throws CategoryNotValidException
     * @throws ExpenseNotValidException
     *
     * @dataProvider executeValidationsDataProvider
     */
    public function testExecuteValidations(string $currentExpenseId, string $exception, string $exceptionMessage): void
    {
        $this->currentExpenseRepository
            ->method('findOneById')
            ->willReturn(is_numeric($currentExpenseId) ? CurrentExpenseStub::createExample($currentExpenseId) : null);

        self::expectException($exception);
        self::expectExceptionMessage($exceptionMessage);

        $this->service->execute($currentExpenseId);
    }

    public static function executeValidationsDataProvider(): array
    {
        return [
            'current expense does not exist' => [
                'currentExpenseId' => 'g23s3',
                'exception' => ExpenseNotFoundException::class,
                'exceptionMessage' => 'Current expense g23s3 does not exist',
            ],
        ];
    }
}
