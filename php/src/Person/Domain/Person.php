<?php

declare(strict_types=1);

namespace App\Person\Domain;

use DateTimeImmutable;
use Ramsey\Uuid\Uuid;

class Person
{
    public readonly string $id;

    private bool $isDeleted = false;

    public readonly DateTimeImmutable $lastModified;

    /**
     * @throws PersonNotValidException
     */
    public function __construct(
        ?string $id,
        public readonly string $name,
        ?DateTimeImmutable $lastModified = null,
    ) {
        $this->id = $id ?? Uuid::uuid7()->toString();
        $this->lastModified = $lastModified ?? new DateTimeImmutable();

        $errors = $this->validate();
        if (!empty($errors)) {
            throw new PersonNotValidException(json_encode($errors));
        }
    }

    private function validate(): array
    {
        $errors = [];

        if ($this->name === '') {
            $errors['name'] = 'Name cannot be empty';
        }
        if (strlen($this->name) > 50) {
            $errors['name'] = 'Name cannot be longer than 50 characters';
        }

        return $errors;
    }
}
