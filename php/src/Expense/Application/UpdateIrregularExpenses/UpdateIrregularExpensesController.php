<?php

declare(strict_types=1);

namespace App\Expense\Application\UpdateIrregularExpenses;

use App\Expense\Domain\UpdateIrregularExpensesService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/expenses/irregular', methods: Request::METHOD_PUT)]
class UpdateIrregularExpensesController extends AbstractController
{
    public function __construct(
        private readonly RequestValidator $requestValidator,
        private readonly RequestToModelsMapper $requestToModelsMapper,
        private readonly UpdateIrregularExpensesService $service,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        try {
            $responseError = $this->requestValidator->execute();
        } catch (RequestNotValidException $exception) {
            return new JsonResponse($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        if ($responseError !== null) {
            return new JsonResponse($responseError, Response::HTTP_BAD_REQUEST);
        }

        try {
            $irregularExpenses = $this->requestToModelsMapper->execute();
        } catch (RequestNotValidException $exception) {
            return new JsonResponse($exception->getMessage(), Response::HTTP_NOT_ACCEPTABLE, json: true);
        }

        $irregularExpenses = $this->service->execute(...$irregularExpenses);
        $this->entityManager->flush();

        return new JsonResponse($irregularExpenses);
    }
}
