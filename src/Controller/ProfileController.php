<?php

namespace App\Controller;

use App\Entity\FavouriteMeal;
use App\Entity\Meals;
use App\Entity\Profile;
use App\Entity\User;
use App\Form\ProfileType;
use App\Service\DietProvider;
use App\Service\UserProfileService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    private UserProfileService $profileService;
    private DietProvider $dietProvider;
    private EntityManagerInterface $entityManager;
    private Security $security;

    public function __construct(UserProfileService $profileService, DietProvider $dietProvider, EntityManagerInterface $entityManager, Security $security)
    {
        $this->profileService = $profileService;
        $this->dietProvider = $dietProvider;
        $this->entityManager = $entityManager;
        $this->security = $security;
    }
    #[Route('/api/profile', name: 'api_profile', methods: ['GET'])]
    public function getProfile(): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $profile = $this->entityManager->getRepository(Profile::class)->findOneBy(['userid' => $user]);

        if (!$profile) {
            return $this->json(['error' => 'Profile not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($profile, Response::HTTP_OK, [], ['groups' => 'profile:read']);
    }

    #[Route('/api/profile', name: 'api_profile_update', methods: ['POST'])]
    public function updateProfile(Request $request): Response
    {
        $user = $this->entityManager->getRepository(User::class)->find($this->security->getUser()->getId());
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $profile = $this->entityManager->getRepository(Profile::class)->findOneBy(['userid' => $user]);
        if (!$profile) {
            $profile = new Profile();
            $profile->setUserid($user);
        }

        $data = json_decode($request->getContent(), true);
        $profile->setHeight($data['height'] ?? $profile->getHeight());
        $profile->setWeight($data['weight'] ?? $profile->getWeight());
        $profile->setAge($data['age'] ?? $profile->getAge());
        $profile->setGender($data['gender'] ?? $profile->getGender());
        $profile->setActivityLevel($data['activitylevel'] ?? $profile->getActivityLevel());
        $profile->setDietPreferences($data['dietpreferences'] ?? $profile->getDietPreferences());
        $profile->setAllergies($data['allergies'] ?? $profile->getAllergies());

        $this->entityManager->persist($profile);
        $this->entityManager->flush();

        return $this->json($profile, Response::HTTP_OK, [], ['groups' => 'profile:read']);
    }

}
