<?php

namespace App\Budget\Application\UpdateBudget;

use App\Budget\Application\UpdateBudget\Request\RequestNotValidException;
use App\Budget\Application\UpdateBudget\Request\RequestToModelsMapper;
use App\Budget\Application\UpdateBudget\Request\RequestValidator;
use App\Budget\Application\UpdateBudget\Response\BudgetToResponseDtoMapper;
use App\Budget\Domain\UpdateBudgetService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UpdateBudgetController extends AbstractController
{
    public function __construct(
        private readonly RequestValidator $requestValidator,
        private readonly RequestToModelsMapper $requestToModelsMapper,
        private readonly UpdateBudgetService $service,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        $responseError = $this->requestValidator->execute();
        if ($responseError !== null) {
            return new JsonResponse($responseError, Response::HTTP_BAD_REQUEST);
        }

        try {
            $budget = $this->requestToModelsMapper->execute();
        } catch (RequestNotValidException $exception) {
            return new JsonResponse($exception->getMessage(), Response::HTTP_NOT_ACCEPTABLE, json: true);
        }

        $budget = $this->service->execute($budget);
        $this->entityManager->flush();

        return new JsonResponse(BudgetToResponseDtoMapper::execute($budget));
    }
}