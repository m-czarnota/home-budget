<?php

namespace App\Person\Application\RemovePerson;

use App\Person\Domain\PersonNotFoundException;
use App\Person\Domain\RemovePersonService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/{id}', requirements: ['id' => '\S+'], methods: Request::METHOD_DELETE)]
class RemovePersonController extends AbstractController
{
    public function __construct(
        private readonly RemovePersonService $service,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(string $id): JsonResponse
    {
        try {
            $this->service->execute($id);
        } catch (PersonNotFoundException $exception) {
            return new JsonResponse($exception->getMessage(), Response::HTTP_NOT_ACCEPTABLE);
        }

        $this->entityManager->flush();

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }
}
