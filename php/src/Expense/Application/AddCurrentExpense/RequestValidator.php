<?php

declare(strict_types=1);

namespace App\Expense\Application\AddCurrentExpense;

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

        if (!isset($data['cost'])) {
            $errors['cost'] = 'Missing `cost` parameter';
        }

        if (!isset($data['category'])) {
            $errors['category'] = 'Missing `category` parameter';
        }

        if (!isset($data['isWish'])) {
            $errors['isWish'] = 'Missing `isWish` parameter';
        }

        if (!isset($data['dateOfExpense'])) {
            $errors['dateOfExpense'] = 'Missing `dateOfExpense` parameter';
        }

        if (!isset($data['people'])) {
            $errors['people'] = 'Missing `people` parameter';
        }

        return $errors;
    }
}
