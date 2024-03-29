<?php

declare(strict_types=1);

namespace App\Person\Domain;

readonly class UpdatePersonService
{
    public function __construct(
        private PersonRepositoryInterface $personRepository,
    ) {
    }

    /**
     * @throws PersonNotFoundException
     */
    public function execute(Person $updatedPerson): Person
    {
        $existedPerson = $this->personRepository->findOneById($updatedPerson->id);
        if (!$existedPerson) {
            throw new PersonNotFoundException("Person `{$updatedPerson->id}` does not exist");
        }

        $existedPerson->update($updatedPerson);

        return $existedPerson;
    }
}
