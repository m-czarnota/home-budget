<?php

declare(strict_types=1);

namespace App\Expense\Application\ListIrregularExpenses;

use App\Expense\Domain\ListIrregularExpensesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/expenses/irregular', methods: Request::METHOD_GET)]
class ListIrregularExpensesController extends AbstractController
{
    public function __construct(
        private readonly ListIrregularExpensesService $service,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        return new JsonResponse($this->service->execute());
    }
}
