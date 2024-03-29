<?php

declare(strict_types=1);

namespace App\Person\Application\UpdatePerson;

use App\Person\Domain\PersonNotFoundException;
use App\Person\Domain\PersonNotValidException;
use App\Person\Domain\UpdatePersonService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/people/{id}', requirements: ['id' => '\S+'], methods: Request::METHOD_PATCH)]
class UpdatePersonController extends AbstractController
{
    public function __construct(
        private readonly RequestValidator $requestValidator,
        private readonly RequestMapperToModel $requestMapperToModel,
        private readonly UpdatePersonService $service,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(string $id): JsonResponse
    {
        $errors = $this->requestValidator->execute();
        if (!empty($errors)) {
            return new JsonResponse($errors, Response::HTTP_BAD_REQUEST);
        }

        try {
            $person = $this->requestMapperToModel->execute($id);
        } catch (PersonNotValidException $exception) {
            return new JsonResponse($exception->getMessage(), Response::HTTP_NOT_ACCEPTABLE, json: true);
        }

        try {
            $person = $this->service->execute($person);
        } catch (PersonNotFoundException $exception) {
            return new JsonResponse($exception->getMessage(), Response::HTTP_NOT_FOUND);
        }
        $this->entityManager->flush();

        return new JsonResponse($person, Response::HTTP_OK);
    }
}
