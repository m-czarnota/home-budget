<?php

namespace App\Category\Application\UpdateCategories;

use App\Category\Domain\UpdateCategoriesService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/category', methods: [Request::METHOD_PUT])]
class UpdateCategoriesController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UpdateCategoriesService $updateCategoriesService,
        private readonly RequestMapperToModels $requestMapperToModels,
        private readonly RequestValidator $requestValidator,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        try {
            $errors = $this->requestValidator->execute();
            if ($errors) {
                return new JsonResponse($errors, Response::HTTP_BAD_REQUEST);
            }
        } catch (RequestNotValidException $exception) {
            return new JsonResponse($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        try {
            $categoryModels = $this->requestMapperToModels->execute();
        } catch (RequestNotValidException $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_NOT_ACCEPTABLE, json: true);
        }

        $categoryModels = $this->updateCategoriesService->execute(...$categoryModels);
        $this->entityManager->flush();

        return new JsonResponse($categoryModels);
    }
}
