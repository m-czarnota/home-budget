<?php

declare(strict_types=1);

namespace App\Person\Domain;

use DateTimeImmutable;
use JsonSerializable;
use Ramsey\Uuid\Uuid;

class Person implements JsonSerializable
{
    public readonly string $id;

    private bool $isDeleted = false;

    private DateTimeImmutable $lastModified;

    /**
     * @throws PersonNotValidException
     */
    public function __construct(
        ?string $id,
        private string $name,
        ?DateTimeImmutable $lastModified = null,
    ) {
        $this->id = $id ?? Uuid::uuid7()->toString();
        $this->lastModified = $lastModified ?? new DateTimeImmutable();

        $errors = $this->validate();
        if (!empty($errors)) {
            throw new PersonNotValidException(json_encode($errors));
        }
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'isDeleted' => $this->isDeleted,
            'lastModified' => $this->lastModified->format('Y-m-d H:i:s'),
        ];
    }

    public function update(self $person): self
    {
        $this->name = $person->getName();
        $this->lastModified = new DateTimeImmutable();

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLastModified(): DateTimeImmutable
    {
        return $this->lastModified;
    }

    private function validate(): array
    {
        $errors = [];

        if ($this->id === '') {
            $errors['id'] = 'ID cannot be empty';
        }
        if (strlen($this->id) > 50) {
            $errors['id'] = 'ID cannot be longer than 50 characters';
        }

        if ($this->name === '') {
            $errors['name'] = 'Name cannot be empty';
        }
        if (mb_strlen($this->name) > 50) {
            $errors['name'] = 'Name cannot be longer than 50 characters';
        }

        return $errors;
    }
}
