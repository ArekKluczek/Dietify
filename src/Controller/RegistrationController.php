<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class RegistrationController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function apiRegister(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        SessionInterface $session
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $plainPassword = $data['plainPassword'] ?? null;
        $secondPassword = $data['secondPassword'] ?? null;

        if (!$email || !$plainPassword || !$secondPassword) {
            return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        if ($plainPassword !== $secondPassword) {
            return new JsonResponse(['error' => 'Passwords do not match!'], Response::HTTP_BAD_REQUEST);
        }

        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            return new JsonResponse(['error' => 'User already exists!'], Response::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
        $user->setSecondPassword($userPasswordHasher->hashPassword($user, $plainPassword));

        try {
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred while saving user: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $session->set('user', $user);

        return new JsonResponse([], Response::HTTP_CREATED);
    }
}
