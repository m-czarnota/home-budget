<?php

namespace App\Person\Application\AddPerson;

use App\Person\Domain\AddPersonService;
use App\Person\Domain\PersonNotValidException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/person', methods: Request::METHOD_POST)]
class AddPersonController extends AbstractController
{
    public function __construct(
        private readonly RequestValidator $requestValidator,
        private readonly RequestMapperToModel $requestMapperToModel,
        private readonly AddPersonService $service,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        $errors = $this->requestValidator->execute();
        if (!empty($errors)) {
            return new JsonResponse($errors, Response::HTTP_BAD_REQUEST);
        }

        try {
            $person = $this->requestMapperToModel->execute();
        } catch (PersonNotValidException $exception) {
            return new JsonResponse($exception->getMessage(), Response::HTTP_NOT_ACCEPTABLE, json: true);
        }

        $person = $this->service->execute($person);
        $this->entityManager->flush();

        return new JsonResponse($person->id, Response::HTTP_CREATED);
    }
}
