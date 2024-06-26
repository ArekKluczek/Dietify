<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\Authenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, Authenticator $formAuthenticator, UserAuthenticatorInterface $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $plainPassword = $form->get('plainPassword')->getData();
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

                return $authenticator->authenticateUser(
                    $user,
                    $formAuthenticator,
                    $request);
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
