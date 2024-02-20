<?php

namespace App\Tests\Person\Unit\Domain;

use App\Person\Domain\Person;
use App\Person\Domain\PersonNotValidException;
use PHPUnit\Framework\TestCase;

class PersonTest extends TestCase
{
    public function testCreateObject(): void
    {
        $person = new Person(null, 'Michal');
        self::assertInstanceOf(Person::class, $person);
    }

    /**
     * @throws PersonNotValidException
     *
     * @dataProvider validationDataProvider
     */
    public function testValidation(string $name, string $exceptionMessage): void
    {
        self::expectException(PersonNotValidException::class);
        self::expectExceptionMessage($exceptionMessage);
        new Person(null, $name);
    }

    public static function validationDataProvider(): array
    {
        return [
            'empty name' => [
                'name' => '',
                'exceptionMessage' => json_encode([
                    'name' => 'Name cannot be empty',
                ]),
            ],
            'too long name' => [
                'name' => 'too long name too long name too long name too long name too long name too long name',
                'exceptionMessage' => json_encode([
                    'name' => 'Name cannot be longer than 50 characters',
                ]),
            ],
        ];
    }
}
