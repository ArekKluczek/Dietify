<?php

// src/Controller/SessionController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SessionController extends AbstractController
{
    #[Route('/api/session', name: 'api_session', methods: ['GET'])]
    public function getSession(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if ($user) {
            return $this->json([
                'username' => $user->getUsername(),
            ]);
        }

        return $this->json(['error' => 'Unauthorized'], 401);
    }
}
