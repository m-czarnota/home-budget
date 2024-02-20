<?php

namespace App\Tests\Person\Unit\Domain;

use App\Person\Domain\PersonNotFoundException;
use App\Person\Domain\PersonNotValidException;
use App\Person\Domain\PersonRepositoryInterface;
use App\Person\Domain\RemovePersonService;
use App\Tests\Person\Stub\PersonStub;
use PHPUnit\Framework\TestCase;

class RemovePersonServiceTest extends TestCase
{
    private readonly PersonRepositoryInterface $personRepository;
    private readonly RemovePersonService $service;

    protected function setUp(): void
    {
        $this->personRepository = $this->createMock(PersonRepositoryInterface::class);
        $this->service = new RemovePersonService(
            $this->personRepository,
        );
    }

    /**
     * @throws PersonNotValidException
     * @throws PersonNotFoundException
     *
     * @dataProvider executeDataProvider
     */
    public function testExecuteValidations(string $personId, string $exceptedException, string $exceptionMessage): void
    {
        $person = $personId !== '' ? PersonStub::createExamplePerson($personId) : null;
        $this->personRepository
            ->method('findOneById')
            ->willReturn($person);

        self::expectException($exceptedException);
        self::expectExceptionMessage($exceptionMessage);

        $this->service->execute($personId);
    }

    public static function executeDataProvider(): array
    {
        return [
            'person not found' => [
                'personId' => '',
                'exceptedException' => PersonNotFoundException::class,
                'exceptionMessage' => 'Person `` does not exist',
            ],
        ];
    }
}
