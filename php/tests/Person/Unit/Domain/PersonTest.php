<?php

declare(strict_types=1);

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
    public function testValidation(string $id, string $name, string $exceptionMessage): void
    {
        self::expectException(PersonNotValidException::class);
        self::expectExceptionMessage($exceptionMessage);
        new Person($id, $name);
    }

    public static function validationDataProvider(): array
    {
        return [
            'empty fields' => [
                'id' => '',
                'name' => '',
                'exceptionMessage' => json_encode([
                    'id' => 'ID cannot be empty',
                    'name' => 'Name cannot be empty',
                ]),
            ],
            'too long fields' => [
                'id' => 'too long id too long id too long id too long id too long id',
                'name' => 'too long name too long name too long name too long name too long name too long name',
                'exceptionMessage' => json_encode([
                    'id' => 'ID cannot be longer than 50 characters',
                    'name' => 'Name cannot be longer than 50 characters',
                ]),
            ],
        ];
    }
}
