<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/dupa', name: 'app_index')]
    public function index(): JsonResponse
    {
        return new JsonResponse('dupa');
    }
}