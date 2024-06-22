<?php

namespace App\Controller;

use http\Client\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class LoginController extends AbstractController
{
    #[Route(path: '/api/login', name: 'api_login', methods: ['POST'])]
    public function apiLogin(Request $request, AuthenticationUtils $authenticationUtils, Security $security): JsonResponse
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($security->getUser()) {
            return new JsonResponse(['status' => 'success', 'message' => 'Already logged in']);
        }

        if ($error) {
            return new JsonResponse(['status' => 'error', 'message' => $error->getMessageKey()]);
        }

        return new JsonResponse(['status' => 'success', 'last_username' => $lastUsername]);
    }

    #[Route(path: '/api/logout', name: 'api_logout', methods: ['POST'])]
    public function apiLogout(): JsonResponse
    {
        // The logout is handled by the firewall, so this should not be called
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}

