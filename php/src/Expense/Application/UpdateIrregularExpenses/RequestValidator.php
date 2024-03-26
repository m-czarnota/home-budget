<?php

declare(strict_types=1);

namespace App\Expense\Application\UpdateIrregularExpenses;

use Symfony\Component\HttpFoundation\RequestStack;

readonly class RequestValidator
{
    public function __construct(
        private RequestStack $requestStack,
    ) {
    }

    /**
     * @throws RequestNotValidException
     */
    public function execute(): ?ResponseError
    {
        $request = $this->requestStack->getCurrentRequest();
        $data = json_decode(trim($request->getContent()), true);

        if (empty($data)) {
            throw new RequestNotValidException('Sent request has not content');
        }

        $isError = false;
        $errors = [];

        foreach ($data as $irregularExpenseData) {
            $irregularExpenseErrors = [];

            if (!isset($irregularExpenseData['name'])) {
                $isError = true;
                $irregularExpenseErrors['name'] = 'Missing `name` parameter';
            }

            if (!isset($irregularExpenseData['cost'])) {
                $isError = true;
                $irregularExpenseErrors['cost'] = 'Missing `cost` parameter';
            }

            if (!isset($irregularExpenseData['category'])) {
                $isError = true;
                $irregularExpenseErrors['category'] = 'Missing `category` parameter';
            }

            if (!isset($irregularExpenseData['position'])) {
                $isError = true;
                $irregularExpenseErrors['position'] = 'Missing `position` parameter';
            }

            $errors[] = $irregularExpenseErrors;
        }

        return $isError
            ? new ResponseError($errors)
            : null;
    }
}
