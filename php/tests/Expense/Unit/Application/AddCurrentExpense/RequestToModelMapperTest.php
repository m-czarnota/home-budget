<?php

declare(strict_types=1);

namespace App\Tests\Expense\Unit\Application\AddCurrentExpense;

use App\Category\Domain\CategoryNotFoundException;
use App\Category\Domain\CategoryNotValidException;
use App\Category\Domain\CategoryRepositoryInterface;
use App\Expense\Application\AddCurrentExpense\RequestToModelMapper;
use App\Expense\Domain\ExpenseNotValidException;
use App\Person\Domain\PersonNotFoundException;
use App\Person\Domain\PersonNotValidException;
use App\Person\Domain\PersonRepositoryInterface;
use App\Tests\Category\Stub\CategoryStub;
use App\Tests\Person\Stub\PersonStub;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestToModelMapperTest extends TestCase
{
    private readonly RequestStack $requestStack;
    private readonly CategoryRepositoryInterface $categoryRepository;
    private readonly PersonRepositoryInterface $personRepository;
    private readonly RequestToModelMapper $service;

    protected function setUp(): void
    {
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->categoryRepository = $this->createMock(CategoryRepositoryInterface::class);
        $this->personRepository = $this->createMock(PersonRepositoryInterface::class);

        $this->service = new RequestToModelMapper(
            $this->requestStack,
            $this->categoryRepository,
            $this->personRepository,
        );
    }

    /**
     * @throws CategoryNotValidException
     * @throws CategoryNotFoundException
     * @throws ExpenseNotValidException
     * @throws PersonNotFoundException
     *
     * @dataProvider executeDataProvider
     */
    public function testExecute(array $currentExpenseData): void
    {
        $request = $this->createMock(Request::class);
        $request
            ->method('getContent')
            ->willReturn(json_encode($currentExpenseData));

        $this->requestStack
            ->method('getCurrentRequest')
            ->willReturn($request);

        $this->categoryRepository
            ->method('findOneById')
            ->willReturn(CategoryStub::createExampleCategory($currentExpenseData['category']));

        $this->personRepository
            ->method('findOneById')
            ->willReturnCallback(fn (string $id) => PersonStub::createExamplePerson($id));

        $currentExpense = $this->service->execute();

        self::assertCount(count($currentExpenseData['people']), $currentExpense->getPeople());
    }

    public static function executeDataProvider(): array
    {
        return [
            'empty people' => [
                'currentExpenseData' => [
                    'name' => 'Pepsi 2-pak',
                    'cost' => 16.99,
                    'category' => '1',
                    'isWish' => true,
                    'dateOfExpense' => '2024-01-17 16:54:27',
                    'people' => [],
                ],
            ],
            '2 people' => [
                'currentExpenseData' => [
                    'name' => 'Pepsi 2-pak',
                    'cost' => 16.99,
                    'category' => '1',
                    'isWish' => true,
                    'dateOfExpense' => '2024-01-17 16:54:27',
                    'people' => ['A', 'M'],
                ],
            ],
        ];
    }

    /**
     * @throws PersonNotFoundException
     * @throws CategoryNotFoundException
     * @throws ExpenseNotValidException
     * @throws CategoryNotValidException
     * @throws PersonNotValidException
     *
     * @dataProvider executeValidationsDataProvider
     */
    public function testExecuteValidations(array $currentExpenseData, string $exceptedException, string $exceptionMessage): void
    {
        $request = $this->createMock(Request::class);
        $request
            ->method('getContent')
            ->willReturn(json_encode($currentExpenseData));

        $this->requestStack
            ->method('getCurrentRequest')
            ->willReturn($request);

        $categoryId = $currentExpenseData['category'] ?? null;
        $this->categoryRepository
            ->method('findOneById')
            ->willReturn(is_numeric($categoryId) ? CategoryStub::createExampleCategory($categoryId) : null);

        $peopleIds = $currentExpenseData['people'] ?? [];
        $this->personRepository
            ->method('findOneById')
            ->willReturnMap(array_map(
                fn (string $id) => [$id, is_numeric($id) ? PersonStub::createExamplePerson($id) : null],
                $peopleIds
            ));

        self::expectException($exceptedException);
        self::expectExceptionMessage($exceptionMessage);

        $this->service->execute();
    }

    public static function executeValidationsDataProvider(): array
    {
        return [
            'category does not exist' => [
                'currentExpenseData' => [
                    'category' => 'M',
                ],
                'exceptedException' => CategoryNotFoundException::class,
                'exceptionMessage' => json_encode(['category' => 'Category `M` does not exist']),
            ],
            'some of people do not exist' => [
                'currentExpenseData' => [
                    'category' => '1',
                    'people' => ['M', '1', 'A'],
                ],
                'exceptedException' => PersonNotFoundException::class,
                'exceptionMessage' => json_encode(['people' => [
                    'Person `M` does not exist',
                    'Person `A` does not exist',
                ]]),
            ],
            'date of expense is not valid' => [
                'currentExpenseData' => [
                    'category' => '1',
                    'people' => ['1'],
                    'dateOfExpense' => 'wrong date',
                ],
                'exceptedException' => InvalidArgumentException::class,
                'exceptionMessage' => json_encode([
                    'dateOfExpense' => 'Date of expense `wrong date` is not valid date time',
                ]),
            ],
        ];
    }
}
