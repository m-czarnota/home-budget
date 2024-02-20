<?php

namespace App\Tests\Person\Unit\Application\AddPerson;

use App\Person\Application\AddPerson\RequestValidator;
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

        foreach ($errors as $index => $error) {
            $exceptedError = $exceptedErrors[$index];

            $exceptedNameError = $exceptedError['name'] ?? null;
            if ($exceptedNameError) {
                self::assertEquals($exceptedNameError, $error['name']);
            }
        }
    }

    public static function executeDataProvider(): array
    {
        return [
            'no errors' => [
                'requestContent' => json_encode([
                    'name' => 'Michal',
                ]),
                'exceptedErrors' => [],
            ],
            'no request content' => [
                'requestContent' => json_encode([]),
                'exceptedErrors' => [
                    'name' => 'Missing `name` parameter',
                ],
            ],
            'other strange data in request content' => [
                'requestContent' => json_encode([
                    'strange param' => 'strange value',
                    'position' => 1,
                ]),
                'exceptedErrors' => [
                    'name' => 'Missing `name` parameter',
                ],
            ],
        ];
    }
}
