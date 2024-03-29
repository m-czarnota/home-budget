<?php

declare(strict_types=1);

namespace App\Person\Application\UpdatePerson;

use Symfony\Component\HttpFoundation\RequestStack;

readonly class RequestValidator
{
    public function __construct(
        private RequestStack $requestStack,
    ) {
    }

    public function execute(): array
    {
        $request = $this->requestStack->getCurrentRequest();
        $data = json_decode(trim($request->getContent()), true);
        $errors = [];

        if (!isset($data['name'])) {
            $errors['name'] = 'Missing `name` parameter';
        }

        return $errors;
    }
}
