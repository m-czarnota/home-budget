<?php

declare(strict_types=1);

namespace App\Category\Application\ListCategories;

use App\Category\Domain\ListCategoriesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/categories', methods: Request::METHOD_GET)]
class ListCategoriesController extends AbstractController
{
    public function __construct(
        private readonly ListCategoriesService $service,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        return new JsonResponse($this->service->execute());
    }
}
