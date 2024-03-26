<?php

declare(strict_types=1);

namespace App\Expense\Application\RemoveCurrentExpense;

use App\Expense\Domain\ExpenseNotFoundException;
use App\Expense\Domain\RemoveCurrentExpenseService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/expenses/current/{id}', requirements: ['id' => '\S+'], methods: Request::METHOD_DELETE)]
class RemoveCurrentExpenseController extends AbstractController
{
    public function __construct(
        private readonly RemoveCurrentExpenseService $service,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(string $id): JsonResponse
    {
        try {
            $this->service->execute($id);
        } catch (ExpenseNotFoundException $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->flush();

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }
}
