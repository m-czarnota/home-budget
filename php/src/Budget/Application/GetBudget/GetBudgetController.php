<?php

namespace App\Budget\Application\GetBudget;

use App\Budget\Application\UpdateBudget\Response\BudgetToResponseDtoMapper;
use App\Budget\Domain\GetBudgetService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/budget', methods: Request::METHOD_GET)]
class GetBudgetController extends AbstractController
{
    public function __construct(
        private readonly GetBudgetService $service,
        private readonly RequestToBudgetPeriodMapper $requestToBudgetPeriodMapper,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        $budgetPeriod = $this->requestToBudgetPeriodMapper->execute();
        $budget = $this->service->execute($budgetPeriod);

        return new JsonResponse(BudgetToResponseDtoMapper::execute($budget));
    }
}