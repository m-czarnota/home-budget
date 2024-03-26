<?php

declare(strict_types=1);

namespace App\Person\Domain;

interface PersonRepositoryInterface
{
    public function add(Person $person): void;

    public function remove(Person $person): void;

    /**
     * @return array<int, Person>
     */
    public function findList(): array;

    public function findOneById(string $id): ?Person;
}
