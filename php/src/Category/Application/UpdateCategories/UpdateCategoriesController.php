<?php

declare(strict_types=1);

namespace App\Category\Application\UpdateCategories;

use App\Category\Application\UpdateCategories\Request\RequestMapperToModels;
use App\Category\Application\UpdateCategories\Request\RequestNotValidException;
use App\Category\Application\UpdateCategories\Request\RequestValidator;
use App\Category\Domain\UpdateCategoriesService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/categories', methods: [Request::METHOD_PUT])]
class UpdateCategoriesController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UpdateCategoriesService $updateCategoriesService,
        private readonly RequestMapperToModels $requestMapperToModels,
        private readonly RequestValidator $requestValidator,
        private readonly CategoryConnectionChecker $categoryConnectionChecker,
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

        $categoriesDeletionInfo = $this->categoryConnectionChecker->execute(...$categoryModels);

        $categoryModels = $this->updateCategoriesService->execute($categoriesDeletionInfo, ...$categoryModels);
        $this->entityManager->flush();

        return new JsonResponse($categoryModels);
    }
}
