<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    private $jwtManager;
    private $entityManager;

    private $request;

    public function __construct(JWTManager $jwtManager, EntityManagerInterface $entityManager, Request $request)
    {
        $this->jwtManager = $jwtManager;
        $this->entityManager = $entityManager;
        $this->request = $request;
    }

    #[Route('/api/user', name: 'api_user', methods: ['GET'])]
    public function getApiUser(): JsonResponse
    {
        $authorizationHeader = $this->request->headers->get('Authorization');

        if (!$authorizationHeader) {
            return new JsonResponse(['status' => 'error', 'message' => 'No token provided'], Response::HTTP_UNAUTHORIZED);
        }

        $token = str_replace('Bearer ', '', $authorizationHeader);

        try {
            $data = $this->jwtManager->decode($token);
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'error', 'message' => 'Invalid token'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        if (!$data) {
            return new JsonResponse(['status' => 'error', 'message' => 'Invalid token'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data['username']]);

        if (!$user) {
            return new JsonResponse(['status' => 'error', 'message' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'user' => [
                'id' => $user->getId(),
                'name' => $user->getUserIdentifier(),
                'email' => $user->getEmail(),
            ],
        ]);
    }
}
