<?php

declare(strict_types=1);

namespace App\Tests\Expense\Unit\Application\UpdateIrregularExpenses;

use App\Category\Domain\CategoryNotValidException;
use App\Category\Domain\CategoryRepositoryInterface;
use App\Expense\Application\UpdateIrregularExpenses\RequestNotValidException;
use App\Expense\Application\UpdateIrregularExpenses\RequestToModelsMapper;
use App\Tests\Category\Stub\CategoryStub;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestToModelsMapperTest extends TestCase
{
    private readonly RequestStack $requestStack;
    private readonly CategoryRepositoryInterface $categoryRepository;
    private readonly RequestToModelsMapper $service;

    protected function setUp(): void
    {
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->categoryRepository = $this->createMock(CategoryRepositoryInterface::class);

        $this->service = new RequestToModelsMapper(
            $this->requestStack,
            $this->categoryRepository,
        );
    }

    /**
     * @throws RequestNotValidException
     * @throws CategoryNotValidException
     *
     * @dataProvider executeDataProvider
     */
    public function testExecute(array $requestData): void
    {
        $request = $this->createMock(Request::class);
        $request
            ->method('getContent')
            ->willReturn(json_encode($requestData));

        $this->requestStack
            ->method('getCurrentRequest')
            ->willReturn($request);

        $this->categoryRepository
            ->method('findOneById')
            ->willReturnMap([
                ['1', CategoryStub::createExampleCategory('1')],
                ['2', CategoryStub::createExampleCategory('2')],
            ]);

        $irregularExpenses = $this->service->execute();

        self::assertCount(count($requestData), $irregularExpenses);

        foreach ($irregularExpenses as $index => $irregularExpense) {
            $irregularExpenseRequestData = $requestData[$index];

            self::assertEquals($irregularExpenseRequestData['name'], $irregularExpense->getName());
            self::assertEquals($irregularExpenseRequestData['cost'], $irregularExpense->getCost());
            self::assertEquals($irregularExpenseRequestData['category'], $irregularExpense->getCategory()->id);
            self::assertEquals($irregularExpenseRequestData['position'], $irregularExpense->getPosition());
            self::assertEquals($irregularExpenseRequestData['isWish'], $irregularExpense->isWish());
            self::assertEquals($irregularExpenseRequestData['plannedYear'], $irregularExpense->getPlannedYear());
        }
    }

    public static function executeDataProvider(): array
    {
        return [
            'only new expenses' => [
                'requestData' => [
                    [
                        'name' => 'Wakacje w Budapeszcie',
                        'cost' => 5200,
                        'category' => '1',
                        'position' => 0,
                        'isWish' => true,
                        'plannedYear' => 2024,
                    ],
                    [
                        'name' => 'Prezenty na święta',
                        'cost' => 1000,
                        'category' => '2',
                        'position' => 1,
                        'isWish' => true,
                        'plannedYear' => 2024,
                    ],
                ],
            ],
            'only existed expenses' => [
                'requestData' => [
                    [
                        'id' => 'irregular-expense-1',
                        'name' => 'Wakacje w Budapeszcie',
                        'cost' => 5200,
                        'category' => '1',
                        'position' => 0,
                        'isWish' => true,
                        'plannedYear' => 2024,
                    ],
                    [
                        'id' => 'irregular-expense-2',
                        'name' => 'Prezenty na święta',
                        'cost' => 1000,
                        'category' => '2',
                        'position' => 1,
                        'isWish' => true,
                        'plannedYear' => 2024,
                    ],
                ],
            ],
        ];
    }

    /**
     * @throws RequestNotValidException
     * @throws CategoryNotValidException
     *
     * @dataProvider executeValidationsDataProvider
     */
    public function testExecuteValidations(array $requestData, string $exceptionMessage): void
    {
        $request = $this->createMock(Request::class);
        $request
            ->method('getContent')
            ->willReturn(json_encode($requestData));

        $this->requestStack
            ->method('getCurrentRequest')
            ->willReturn($request);

        $this->categoryRepository
            ->method('findOneById')
            ->willReturnMap([
                ['', null],
                ['non existed category', null],
                ['1', CategoryStub::createExampleCategory('1')],
            ]);

        self::expectException(RequestNotValidException::class);
        self::expectExceptionMessage($exceptionMessage);

        $this->service->execute();
    }

    public static function executeValidationsDataProvider(): array
    {
        $currentDate = new DateTimeImmutable();
        $currentYear = intval($currentDate->format('Y'));

        return [
            'empty request content' => [
                'requestData' => [],
                'exceptionMessage' => 'Sent request has not content',
            ],
            'no categories and non-existed categories' => [
                'requestData' => [
                    [
                        'name' => 'random name 1',
                    ],
                    [],
                    [
                        'name' => 'random name 3',
                        'category' => 'non existed category',
                    ],
                ],
                'exceptionMessage' => json_encode([
                    [
                        'category' => 'Category `` does not exist',
                    ],
                    [
                        'category' => 'Category `` does not exist',
                    ],
                    [
                        'category' => 'Category `non existed category` does not exist',
                    ],
                ]),
            ],
            'wrong expenses data in request' => [
                'requestData' => [
                    [
                        'name' => 'Random name Random name Random name Random name Random name Random name Random name Random name Random name Random name Random name Random name Random name Random name Random name Random name Random name Random name Random name Random name Random name Random name Random name Random name',
                        'cost' => -4,
                        'position' => 4,
                        'category' => '1',
                        'isWish' => false,
                        'plannedYear' => $currentYear,
                    ],
                    [
                        'name' => '',
                        'cost' => 0,
                        'position' => -3,
                        'category' => '1',
                        'isWish' => true,
                        'plannedYear' => $currentYear - 1,
                    ],
                ],
                'exceptionMessage' => json_encode([
                    [
                        'name' => 'Name cannot be longer than 255 characters',
                        'cost' => 'Cost cannot be negative',
                    ],
                    [
                        'name' => 'Name cannot be empty',
                        'cost' => 'Cost cannot be 0',
                        'position' => 'Position cannot be negative',
                        'plannedYear' => "Planned year cannot be earlier than current year $currentYear",
                    ],
                ]),
            ],
        ];
    }
}
