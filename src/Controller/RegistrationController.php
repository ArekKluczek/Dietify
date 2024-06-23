<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\Authenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function apiRegister(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, Authenticator $formAuthenticator, UserAuthenticatorInterface $authenticator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->submit($data);

        $plainPassword = $data['plainPassword'] ?? null;
        $secondPassword = $data['secondPassword'] ?? null;

        if ($form->isSubmitted() && $form->isValid()) {
            if ($plainPassword !== $secondPassword) {
                return new JsonResponse(['error' => 'Passwords do not match!'], Response::HTTP_BAD_REQUEST);
            } else {
                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
                $entityManager->persist($user);
                $entityManager->flush();

                $authenticator->authenticateUser($user, $formAuthenticator, $request);

                return new JsonResponse(['message' => 'User registered successfully'], Response::HTTP_CREATED);
            }
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
    }
}
