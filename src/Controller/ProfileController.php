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
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    #[Route('/api/profile', name: 'api_profile_get', methods: ['GET'])]
    public function getProfile(): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($this->security->getUser()->getId());

        if (!$user) {
            throw new AccessDeniedException('User not logged in.');
        }

        $profile = $this->entityManager->getRepository(Profile::class)->findOneBy(['userid' => $user]);

        if (!$profile) {
            return new JsonResponse([
                'id' => '',
                'height' => '',
                'weight' => '',
                'age' => '',
                'gender' => '',
                'activitylevel' => '',
                'dietpreferences' => '',
                'allergies' => '',
            ]);
        }
        return new JsonResponse([
            'id' => $profile->getId(),
            'height' => $profile->getHeight(),
            'weight' => $profile->getWeight(),
            'age' => $profile->getAge(),
            'gender' => $profile->getGender(),
            'activitylevel' => $profile->getActivityLevel(),
            'dietpreferences' => $profile->getDietPreferences(),
            'allergies' => $profile->getAllergies(),
        ]);
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

    #[Route('/api/profile/bmi', name: 'api_profile_bmi', methods: ['GET'])]
    public function getBMI(): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }
        $profile = $this->entityManager->getRepository(Profile::class)->findOneBy(['userid' => $user]);

        if (!$profile) {
            return new JsonResponse(['error' => 'Profile not found'], 404);
        }

        $bmiResult = $this->profileService->calculateBMI($profile);

        return new JsonResponse([
            'bmiValue' => $bmiResult['value'],
            'bmiCategory' => $bmiResult['category'],
        ]);
    }

    #[Route('/api/profile/diet', name: 'api_profile_diet', methods: ['POST'])]
    public function generateDiet(): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $profile = $this->entityManager->getRepository(Profile::class)->findOneBy(['userid' => $user]);
        if (!$profile) {
            return new JsonResponse(['error' => 'Profile not found'], Response::HTTP_NOT_FOUND);
        }

        $prompt = $this->renderView('diet/diet_prompt.html.twig', $this->profileService->getDietPromptData($profile));
        $jsonResponse = $this->dietProvider->makePlan($prompt);

        if ($jsonResponse) {
            $this->dietProvider->makeDiet($jsonResponse, $user);
            return new JsonResponse(['status' => 'Diet generated successfully']);
        }

        return new JsonResponse(['error' => 'Failed to generate diet'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    #[Route('/api/profile/diet/show', name: 'api_profile_diet_show', methods: ['GET'])]
    public function showDiet(): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $meals = $this->entityManager->getRepository(Meals::class)->findMealsForLatestWeek($user->getId());

        if (!$meals) {
            $profile = $this->entityManager->getRepository(Profile::class)->findOneBy(['userid' => $user]);
            if (!$profile) {
                return new JsonResponse(['error' => 'Profile not found'], Response::HTTP_NOT_FOUND);
            }

            $prompt = $this->renderView('diet/diet_prompt.html.twig', $this->profileService->getDietPromptData($profile));
            $jsonResponse = $this->dietProvider->makePlan($prompt);

            if ($jsonResponse) {
                $this->dietProvider->makeDiet($jsonResponse, $user);
                $meals = $this->entityManager->getRepository(Meals::class)->findMealsForLatestWeek($user->getId());
            } else {
                return new JsonResponse(['error' => 'Failed to generate diet'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        $organizedMeals = [];
        foreach ($meals as $meal) {
            $day = $meal->getDayOfWeek();
            $mealId = $meal->getId();

            $organizedMeals[$day][] = $this->dietProvider->organizeMealData($meal, $mealId);
        }

        $favoriteMeals = $this->entityManager->getRepository(FavouriteMeal::class)->findBy(['user' => $user]);
        $favoriteMealsIds = [];
        foreach ($favoriteMeals as $favoriteMeal) {
            $favoriteMealsIds[] = sprintf("%d-%s", $favoriteMeal->getMealId()->getId(), $favoriteMeal->getMealType());
        }

        return new JsonResponse([
            'mealPlans' => $organizedMeals,
            'favoriteMealsIds' => $favoriteMealsIds,
        ]);
    }
    #[Route('/api/profile/diet/shopping-list', name: 'api_profile_diet_list', methods: ['GET'])]
    public function downloadShoppingList(): Response
    {
        $csvContent = $this->dietProvider->getShoppingList();

        return new Response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="shopping_list_' . date('Y-m-d') . '.csv"',
        ]);
    }

    #[Route('/profile/info', name: 'app_profile_info')]
    public function howItWorks(): Response
    {
        return $this->render('profile/info.html.twig', );
    }
}
