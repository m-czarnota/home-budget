<?php

namespace App\Person\Application\AddPerson;

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
    public function execute(): Person
    {
        $request = $this->requestStack->getCurrentRequest();
        $data = json_decode(trim($request->getContent()), true);

        return new Person(
            null,
            $data['name'] ?? '',
        );
    }
}
