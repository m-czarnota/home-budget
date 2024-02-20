<?php

namespace App\Person\Domain;

readonly class AddPersonService
{
    public function __construct(
        private PersonRepositoryInterface $personRepository,
    ) {
    }

    public function execute(Person $person): Person
    {
        $this->personRepository->add($person);

        return $person;
    }
}
