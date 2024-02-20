<?php

namespace App\Person\Domain;

readonly class RemovePersonService
{
    public function __construct(
        private PersonRepositoryInterface $personRepository,
    ) {
    }

    /**
     * @throws PersonNotFoundException
     */
    public function execute(string $id): void
    {
        $person = $this->personRepository->findOneById($id);
        if ($person === null) {
            throw new PersonNotFoundException("Person `$id` does not exist");
        }

        $this->personRepository->remove($person);
    }
}
