<?php

declare(strict_types=1);

namespace App\Person\Application\UpdatePerson;

use App\Person\Domain\Person;
use App\Person\Domain\PersonNotValidException;
use Symfony\Component\HttpFoundation\RequestStack;

readonly class RequestMapperToModel
{
    public function __construct(
        private RequestStack $requestStack,
    ) {
    }

    /**
     * @throws PersonNotValidException
     */
    public function execute(string $id): Person
    {
        $request = $this->requestStack->getCurrentRequest();
        $data = json_decode(trim($request->getContent()), true);

        return new Person(
            $id,
            $data['name'] ?? '',
        );
    }
}
