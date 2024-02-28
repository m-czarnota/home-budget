<?php

namespace App\Expense\Application\AddCurrentExpense;

use App\Category\Domain\CategoryNotFoundException;
use App\Expense\Domain\AddCurrentExpenseService;
use App\Expense\Domain\ExpenseNotValidException;
use App\Person\Domain\PersonNotFoundException;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/current', methods: Request::METHOD_POST)]
class AddCurrentExpenseController extends AbstractController
{
    public function __construct(
        private readonly RequestValidator $requestValidator,
        private readonly RequestToModelMapper $requestToModelMapper,
        private readonly AddCurrentExpenseService $service,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        $errors = $this->requestValidator->execute();
        if (!empty($errors)) {
            return new JsonResponse($errors, Response::HTTP_BAD_REQUEST);
        }

        try {
            $currentExpense = $this->requestToModelMapper->execute();
        } catch (CategoryNotFoundException|ExpenseNotValidException|PersonNotFoundException|InvalidArgumentException $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_NOT_ACCEPTABLE, json: true);
        }

        try {
            $currentExpense = $this->service->execute($currentExpense);
        } catch (ExpenseNotValidException $exception) {
            return new JsonResponse($exception->getMessage(), Response::HTTP_NOT_ACCEPTABLE);
        }

        return new JsonResponse($currentExpense->id, Response::HTTP_CREATED);
    }
}
