<?php

declare(strict_types=1);

namespace App\Tests\Expense\Unit\Domain;

use App\Expense\Domain\ExpenseNotValidException;
use App\Expense\Domain\IrregularExpenseRepositoryInterface;
use App\Expense\Domain\ListIrregularExpensesService;
use App\Tests\Expense\Stub\IrregularExpenseStub;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ListIrregularExpensesServiceTest extends TestCase
{
    private readonly IrregularExpenseRepositoryInterface $irregularExpenseRepository;
    private readonly ListIrregularExpensesService $service;

    protected function setUp(): void
    {
        $this->irregularExpenseRepository = $this->createMock(IrregularExpenseRepositoryInterface::class);
        $this->service = new ListIrregularExpensesService(
            $this->irregularExpenseRepository,
        );
    }

    /**
     * @throws ExpenseNotValidException
     *
     * @dataProvider executeDataProvider
     */
    public function testExecute(array $dataInDb, int $expectedCount): void
    {
        $irregularExpensesInDb = IrregularExpenseStub::createMultipleFromArrayData($dataInDb);
        $this->irregularExpenseRepository
            ->method('findList')
            ->willReturn($irregularExpensesInDb);

        $irregularExpenses = $this->service->execute();
        self::assertCount($expectedCount, $irregularExpenses);
    }

    public static function executeDataProvider(): array
    {
        $currentDate = new DateTimeImmutable();
        $currentYear = intval($currentDate->format('Y'));

        return [
            'no data' => [
                'dataInDb' => [],
                'exceptedCount' => 0,
            ],
            '3 irregular expenses in db and 3 expected' => [
                'dataInDb' => [
                    [
                        'id' => '1',
                        'name' => 'Irregular Expense 1',
                        'cost' => 1000,
                        'category' => '1',
                        'isWish' => false,
                        'position' => 0,
                        'plannedYear' => $currentYear,
                    ],
                    [
                        'id' => '2',
                        'name' => 'Irregular Expense 2',
                        'cost' => 1400,
                        'category' => '2',
                        'isWish' => true,
                        'position' => 1,
                        'plannedYear' => $currentYear + 1,
                    ],
                    [
                        'id' => '3',
                        'name' => 'Irregular Expense 3',
                        'cost' => 600,
                        'category' => '1',
                        'isWish' => false,
                        'position' => 2,
                        'plannedYear' => $currentYear,
                    ],
                ],
                'exceptedCount' => 3,
            ],
        ];
    }
}
