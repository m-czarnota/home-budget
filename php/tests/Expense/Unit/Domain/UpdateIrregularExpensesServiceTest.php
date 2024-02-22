<?php

namespace App\Tests\Expense\Unit\Domain;

use App\Category\Domain\CategoryNotValidException;
use App\Expense\Domain\ExpenseNotValidException;
use App\Expense\Domain\IrregularExpenseRepositoryInterface;
use App\Expense\Domain\UpdateIrregularExpensesService;
use App\Tests\Expense\Stub\IrregularExpenseStub;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class UpdateIrregularExpensesServiceTest extends TestCase
{
    private readonly IrregularExpenseRepositoryInterface $irregularExpenseRepository;
    private readonly UpdateIrregularExpensesService $service;

    protected function setUp(): void
    {
        $this->irregularExpenseRepository = $this->createMock(IrregularExpenseRepositoryInterface::class);
        $this->service = new UpdateIrregularExpensesService(
            $this->irregularExpenseRepository,
        );
    }

    /**
     * @throws ExpenseNotValidException
     * @throws CategoryNotValidException
     *
     * @dataProvider executeDataProvider
     */
    public function testExec(array $irregularExpensesData, array $existedIrregularExpenses, array $exceptedIrregularExpensesData): void
    {
        $irregularExpensesToUpdate = IrregularExpenseStub::createMultipleFromArrayData($irregularExpensesData);
        $exceptedIrregularExpenses = IrregularExpenseStub::createMultipleFromArrayData($exceptedIrregularExpensesData);

        $this->irregularExpenseRepository
            ->method('findOneById')
            ->willReturnMap(array_map(
                fn (string $id) => [$id, IrregularExpenseStub::createExample($id)],
                $existedIrregularExpenses
            ));

        $irregularExpensesUpdated = $this->service->execute(...$irregularExpensesToUpdate);
        self::assertCount(count($exceptedIrregularExpenses), $irregularExpensesUpdated);

        foreach ($irregularExpensesUpdated as $index => $irregularExpenseUpdated) {
            $exceptedIrregularExpenseId = $exceptedIrregularExpensesData['id'] ?? null;
            if ($exceptedIrregularExpenseId) {
                self::assertEquals($exceptedIrregularExpenseId, $irregularExpenseUpdated->id);
            }

            $exceptedIrregularExpense = $exceptedIrregularExpenses[$index];

            self::assertEquals($exceptedIrregularExpense->getName(), $irregularExpenseUpdated->getName());
            self::assertEquals($exceptedIrregularExpense->getCost(), $irregularExpenseUpdated->getCost());
            self::assertEquals($exceptedIrregularExpense->getPosition(), $irregularExpenseUpdated->getPosition());
            self::assertEquals($exceptedIrregularExpense->isWish(), $irregularExpenseUpdated->isWish());
            self::assertEquals($exceptedIrregularExpense->getPlannedYear(), $irregularExpenseUpdated->getPlannedYear());
        }
    }

    public static function executeDataProvider(): array
    {
        $currentDate = new DateTimeImmutable();
        $currentYear = intval($currentDate->format('Y'));

        return [
            'new irregular expenses' => [
                'irregularExpensesData' => [
                    [
                        'name' => 'Wakacje w Budapeszcie',
                        'cost' => 5200,
                        'category' => '1',
                        'position' => 0,
                        'isWish' => true,
                        'plannedYear' => $currentYear,
                    ],
                    [
                        'name' => 'Prezenty na święta',
                        'cost' => 1000,
                        'category' => '2',
                        'position' => 1,
                        'isWish' => true,
                        'plannedYear' => $currentYear,
                    ],
                ],
                'existedIrregularExpenses' => [],
                'exceptedIrregularExpensesData' => [
                    [
                        'name' => 'Wakacje w Budapeszcie',
                        'cost' => 5200,
                        'category' => '1',
                        'position' => 0,
                        'isWish' => true,
                        'plannedYear' => $currentYear,
                    ],
                    [
                        'name' => 'Prezenty na święta',
                        'cost' => 1000,
                        'category' => '2',
                        'position' => 1,
                        'isWish' => true,
                        'plannedYear' => $currentYear,
                    ],
                ],
            ],
            'update irregular expenses' => [
                'irregularExpensesData' => [
                    [
                        'id' => '3',
                        'name' => 'Wakacje w Sztokholmie',
                        'cost' => 5500,
                        'category' => '4',
                        'position' => 2,
                        'isWish' => true,
                        'plannedYear' => $currentYear + 1,
                    ],
                    [
                        'id' => '4',
                        'name' => 'Prezenty dla rodziców',
                        'cost' => 1200,
                        'category' => '6',
                        'position' => 5,
                        'isWish' => false,
                        'plannedYear' => $currentYear,
                    ],
                ],
                'existedIrregularExpenses' => ['3', '4'],
                'exceptedIrregularExpensesData' => [
                    [
                        'id' => '3',
                        'name' => 'Wakacje w Sztokholmie',
                        'cost' => 5500,
                        'category' => '4',
                        'position' => 2,
                        'isWish' => true,
                        'plannedYear' => $currentYear + 1,
                    ],
                    [
                        'id' => '4',
                        'name' => 'Prezenty dla rodziców',
                        'cost' => 1200,
                        'category' => '6',
                        'position' => 5,
                        'isWish' => false,
                        'plannedYear' => $currentYear,
                    ],
                ],
            ],
            'removed one categories and added two' => [
                'irregularExpensesData' => [
                    [
                        'id' => '1',
                        'name' => 'Wakacje w Budapeszcie',
                        'cost' => 5200,
                        'category' => '1',
                        'position' => 0,
                        'isWish' => true,
                        'plannedYear' => $currentYear,
                    ],
                    [
                        'id' => '2',
                        'name' => 'Prezenty na święta',
                        'cost' => 1000,
                        'category' => '2',
                        'position' => 1,
                        'isWish' => true,
                        'plannedYear' => $currentYear,
                    ],
                    [
                        'name' => 'Wakacje w Sztokholmie',
                        'cost' => 5500,
                        'category' => '4',
                        'position' => 2,
                        'isWish' => true,
                        'plannedYear' => $currentYear + 1,
                    ],
                    [
                        'name' => 'Prezenty dla rodziców',
                        'cost' => 1200,
                        'category' => '6',
                        'position' => 5,
                        'isWish' => false,
                        'plannedYear' => $currentYear,
                    ],
                ],
                'existedIrregularExpenses' => ['1', '2', '3'],
                'exceptedIrregularExpensesData' => [
                    [
                        'id' => '1',
                        'name' => 'Wakacje w Budapeszcie',
                        'cost' => 5200,
                        'category' => '1',
                        'position' => 0,
                        'isWish' => true,
                        'plannedYear' => $currentYear,
                    ],
                    [
                        'id' => '2',
                        'name' => 'Prezenty na święta',
                        'cost' => 1000,
                        'category' => '2',
                        'position' => 1,
                        'isWish' => true,
                        'plannedYear' => $currentYear,
                    ],
                    [
                        'name' => 'Wakacje w Sztokholmie',
                        'cost' => 5500,
                        'category' => '4',
                        'position' => 2,
                        'isWish' => true,
                        'plannedYear' => $currentYear + 1,
                    ],
                    [
                        'name' => 'Prezenty dla rodziców',
                        'cost' => 1200,
                        'category' => '6',
                        'position' => 5,
                        'isWish' => false,
                        'plannedYear' => $currentYear,
                    ],
                ],
            ],
        ];
    }
}
