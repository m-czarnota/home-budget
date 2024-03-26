<?php

declare(strict_types=1);

namespace App\Person\Domain;

readonly class ListPersonService
{
    public function __construct(
        private PersonRepositoryInterface $personRepository,
    ) {
    }

    /**
     * @return array<int, Person>
     */
    public function execute(): array
    {
        return $this->personRepository->findList();
    }
}
