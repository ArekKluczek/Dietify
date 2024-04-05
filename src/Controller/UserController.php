<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;


class UserController extends AbstractController
{
    #[Route('/user/{id}', name: 'app_user', methods: ['GET', 'POST'])]
    public function index(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        $plainPassword = $form->get('password')->getData();
        $secondPassword = $form->get('secondPassword')->getData();
        if ($form->isSubmitted() && $form->isValid()) {
            if ($plainPassword !== $secondPassword) {
                $form->get('secondPassword')->addError(new FormError("Password does not match!"));
            } else {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $plainPassword
                    )
                );

                $user->setSecondPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $secondPassword
                    )
                );

                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirectToRoute('app_dashboard');
            }
        }
        return $this->render('user/index.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
