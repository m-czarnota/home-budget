<?php

declare(strict_types=1);

namespace App\Tests\Person\Unit\Domain;

use App\Person\Domain\PersonNotFoundException;
use App\Person\Domain\PersonNotValidException;
use App\Person\Domain\PersonRepositoryInterface;
use App\Person\Domain\UpdatePersonService;
use App\Tests\Person\Stub\PersonStub;
use PHPUnit\Framework\TestCase;

class UpdatePersonServiceTest extends TestCase
{
    private readonly PersonRepositoryInterface $personRepository;
    private readonly UpdatePersonService $service;

    protected function setUp(): void
    {
        $this->personRepository = $this->createMock(PersonRepositoryInterface::class);
        $this->service = new UpdatePersonService(
            $this->personRepository
        );
    }

    /**
     * @throws PersonNotFoundException
     * @throws PersonNotValidException
     *
     * @dataProvider executeDataProvider
     */
    public function testExecute(array $existedPersonData, array $updatedPersonData): void
    {
        $existedPerson = PersonStub::createFromArrayData($existedPersonData);
        $this->personRepository
            ->method('findOneById')
            ->willReturn($existedPerson);

        $updatedPerson = PersonStub::createFromArrayData($updatedPersonData);
        $person = $this->service->execute($updatedPerson);

        self::assertSame($updatedPerson->id, $person->id);
        self::assertSame($updatedPerson->getName(), $person->getName());
        self::assertNotSame($existedPersonData['lastModified'], $person->getLastModified()->format('Y-m-d H:i:s'));
    }

    public static function executeDataProvider(): array
    {
        return [
            'update person name' => [
                'existedPersonData' => [
                    'id' => 'person 1',
                    'name' => 'person old',
                    'lastModified' => '2000-05-12 23:54:21',
                ],
                'updatedPersonData' => [
                    'id' => 'person 1',
                    'name' => 'person new',
                ],
            ],
        ];
    }

    /**
     * @throws PersonNotFoundException
     * @throws PersonNotValidException
     *
     * @dataProvider executeValidationsDataProvider
     */
    public function testExecuteValidations(array $existedPersonData, array $updatedPersonData, string $exception, string $exceptionMessage): void
    {
        $this->personRepository
            ->method('findOneById')
            ->willReturn(
                $existedPersonData['id'] === $updatedPersonData['id']
                    ? PersonStub::createFromArrayData($existedPersonData)
                    : null
            );

        self::expectException($exception);
        self::expectExceptionMessage($exceptionMessage);

        $updatedPersonData = PersonStub::createFromArrayData($updatedPersonData);
        $this->service->execute($updatedPersonData);
    }

    public function executeValidationsDataProvider(): array
    {
        return [
            'person to update does not exist' => [
                'existedPersonData' => [
                    'id' => 'gdfg',
                    'name' => 'random',
                ],
                'updatedPersonData' => [
                    'id' => 'fdgdg',
                    'name' => 'updated random',
                ],
                'exception' => PersonNotFoundException::class,
                'exceptionMessage' => 'Person `fdgdg` does not exist',
            ],
        ];
    }
}
