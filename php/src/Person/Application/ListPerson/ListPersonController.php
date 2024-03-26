<?php

declare(strict_types=1);

namespace App\Person\Application\ListPerson;

use App\Person\Domain\ListPersonService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/people', methods: Request::METHOD_GET)]
class ListPersonController extends AbstractController
{
    public function __construct(
        private readonly ListPersonService $service,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        $persons = $this->service->execute();

        return new JsonResponse($persons);
    }
}
