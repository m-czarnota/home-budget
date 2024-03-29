<?php

declare(strict_types=1);

namespace App\Tests\Person\Unit\Application\UpdatePerson;

use App\Person\Application\UpdatePerson\RequestMapperToModel;
use App\Person\Domain\Person;
use App\Person\Domain\PersonNotValidException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestMapperToModelTest extends TestCase
{
    private readonly RequestStack $requestStack;
    private readonly RequestMapperToModel $service;

    protected function setUp(): void
    {
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->service = new RequestMapperToModel(
            $this->requestStack,
        );
    }

    /**
     * @throws PersonNotValidException
     *
     * @dataProvider executeDataProvider
     */
    public function testExecute(string $id, string $requestContent): void
    {
        $request = $this->createMock(Request::class);
        $request
            ->method('getContent')
            ->willReturn($requestContent);

        $this->requestStack
            ->method('getCurrentRequest')
            ->willReturn($request);

        $person = $this->service->execute($id);
        self::assertInstanceOf(Person::class, $person);
    }

    public function executeDataProvider(): array
    {
        return [
            'example person' => [
                'id' => 'example-person',
                'requestContent' => json_encode([
                    'name' => 'Example person',
                ]),
            ],
        ];
    }

    /**
     * @throws PersonNotValidException
     *
     * @dataProvider executeValidationsDataProvider
     */
    public function testExecuteValidations(string $id, string $requestContent, string $exceptedException, string $exceptedMessage): void
    {
        $request = $this->createMock(Request::class);
        $request
            ->method('getContent')
            ->willReturn($requestContent);

        $this->requestStack
            ->method('getCurrentRequest')
            ->willReturn($request);

        self::expectException($exceptedException);
        self::expectExceptionMessage($exceptedMessage);

        $this->service->execute($id);
    }

    public static function executeValidationsDataProvider(): array
    {
        return [
            'no request content' => [
                'id' => '',
                'requestContent' => json_encode([]),
                'exceptedException' => PersonNotValidException::class,
                'exceptedMessage' => json_encode([
                    'id' => 'ID cannot be empty',
                    'name' => 'Name cannot be empty',
                ]),
            ],
            'empty fields' => [
                'id' => '',
                'requestContent' => json_encode([
                    'name' => '',
                ]),
                'exceptedException' => PersonNotValidException::class,
                'exceptedMessage' => json_encode([
                    'id' => 'ID cannot be empty',
                    'name' => 'Name cannot be empty',
                ]),
            ],
            'too long fields' => [
                'id' => 'too long id too long id too long id too long id too long id',
                'requestContent' => json_encode([
                    'name' => 'too long name too long name too long name too long name',
                ]),
                'exceptedException' => PersonNotValidException::class,
                'exceptedMessage' => json_encode([
                    'id' => 'ID cannot be longer than 50 characters',
                    'name' => 'Name cannot be longer than 50 characters',
                ]),
            ],
        ];
    }
}
