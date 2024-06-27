<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserController extends AbstractController
{
    #[Route('/api/user', name: 'api_user', methods: ['GET'])]
    public function getApiUser(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            throw new AccessDeniedException('User not logged in.');
        }

        return new JsonResponse([
            'id' => $user->getId(),
            'name' => $user->getEmail(),
            'email' => $user->getEmail(),
        ]);
    }
}
