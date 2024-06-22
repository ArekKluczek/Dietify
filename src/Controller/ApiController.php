<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api/profile', name: 'api_profile')]
    public function profile(): JsonResponse
    {
        // Fetch profile data
        return $this->json([
            'weight' => 70,
            'height' => 175,
        ]);
    }
}