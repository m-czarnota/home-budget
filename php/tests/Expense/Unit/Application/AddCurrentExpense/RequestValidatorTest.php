<?php

namespace App\Tests\Expense\Unit\Application\AddCurrentExpense;

use App\Expense\Application\AddCurrentExpense\RequestValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestValidatorTest extends TestCase
{
    private readonly RequestStack $requestStack;
    private readonly RequestValidator $service;

    protected function setUp(): void
    {
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->service = new RequestValidator(
            $this->requestStack,
        );
    }

    /**
     * @dataProvider executeDataProvider
     */
    public function testExecute(string $requestContent, array $exceptedErrors): void
    {
        $request = $this->createMock(Request::class);
        $request
            ->method('getContent')
            ->willReturn($requestContent);

        $this->requestStack
            ->method('getCurrentRequest')
            ->willReturn($request);

        $errors = $this->service->execute();
        self::assertCount(count($exceptedErrors), $errors);

        foreach ($exceptedErrors as $errorName => $errorMessage) {
            self::assertArrayHasKey($errorName, $errors);
            self::assertEquals($errorMessage, $errors[$errorName]);
        }
    }

    public static function executeDataProvider(): array
    {
        return [
            'no errors' => [
                'requestContent' => json_encode([
                    'name' => 'Pepsi 2-pak',
                    'cost' => 16.99,
                    'category' => '1',
                    'isWish' => true,
                    'dateOfExpense' => '2024-01-23 16:32:54',
                    'people' => ['1'],
                ]),
                'exceptedErrors' => [],
            ],
            'no request content' => [
                'requestContent' => json_encode([]),
                'exceptedErrors' => [
                    'name' => 'Missing `name` parameter',
                    'cost' => 'Missing `cost` parameter',
                    'category' => 'Missing `category` parameter',
                    'isWish' => 'Missing `isWish` parameter',
                    'dateOfExpense' => 'Missing `dateOfExpense` parameter',
                    'people' => 'Missing `people` parameter',
                ],
            ],
        ];
    }
}
