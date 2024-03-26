<?php

declare(strict_types=1);

namespace App\Tests\Person\Stub;

use App\Person\Domain\Person;
use App\Person\Domain\PersonNotValidException;
use DateTimeImmutable;
use Exception;

class PersonStub
{
    /**
     * @return array<int, Person>
     *
     * @throws PersonNotValidException
     */
    public static function createMultipleFromArrayData(array $peopleData): array
    {
        return array_map(fn (array $personData) => self::createFromArrayData($personData), $peopleData);
    }

    /**
     * @throws PersonNotValidException
     * @throws Exception
     */
    public static function createFromArrayData(array $personData): Person
    {
        return new Person(
            $personData['id'] ?? null,
            $personData['name'],
            new DateTimeImmutable($personData['lastModified'] ?? 'now'),
        );
    }

    /**
     * @throws PersonNotValidException
     */
    public static function createExamplePerson(?string $id = null): Person
    {
        return new Person(
            $id,
            'Example Person',
        );
    }
}
