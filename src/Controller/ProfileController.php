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

    #[Route('/profile', name: 'app_profile')]
    public function index(Request $request): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->entityManager->getRepository(User::class)->find($this->security->getUser()->getId());
        $profile = $this->entityManager->getRepository(Profile::class)->findOneBy(['userid' => $user]);

        if (!$profile) {
            $profile = new Profile();
        }

        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profile->setUserid($user);
            $this->entityManager->persist($profile);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('profile/_form.html.twig', [
            'profileForm' => $form->createView(),
        ]);
    }

    #[Route('/profile/bmi', name: 'app_profile_bmi')]
    public function calculateBMI(): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $profile = $this->entityManager->getRepository(Profile::class)->find($this->security->getUser()->getId());

        if (!$profile) {
            return $this->redirectToRoute('app_profile');
        }

        $bmiResult = $this->profileService->calculateBMI($profile);

        return $this->render('profile/bmi.html.twig', [
            'bmiValue' => $bmiResult['value'],
            'bmiCategory' => $bmiResult['category'],
        ]);
    }

    #[Route('/profile/diet', name: 'app_profile_diet', methods: ['GET', 'POST'])]
    public function generate(): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $profile = $this->entityManager->getRepository(Profile::class)->findByUserId($user->getId());
        if (!$profile) {
            return $this->redirectToRoute('app_profile');
        }

        $jsonResponse = null;
        $prompt = $this->renderView('diet/diet_prompt.html.twig', $this->profileService->getDietPromptData($profile));

        if ($this->dietProvider->canUserGeneratePlanThisWeek($user)) {
            $jsonResponse = $this->dietProvider->makePlan($prompt);
        }

        if ($jsonResponse) {
            $this->dietProvider->makeDiet($jsonResponse, $user);
            return $this->redirectToRoute('app_profile_diet_show');
        }

        return $this->redirectToRoute('app_profile_info');
    }

    #[Route('/profile/diet/show', name: 'app_profile_diet_show', methods: ['GET'])]
    public function showDiet(): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $meals = $this->entityManager->getRepository(Meals::class)->findMealsForLatestWeek();

        if (!$meals) {
            return $this->redirectToRoute('app_profile_diet');
        }

        $organizedMeals = [];
        foreach ($meals as $meal) {
            $day = $meal->getDayOfWeek();
            $mealId = $meal->getId();

            $organizedMeals[$day][] = $this->dietProvider->organizeMealData($meal, $mealId);
        }

        $favoriteMeals = $this->entityManager->getRepository(FavouriteMeal::class)->findBy(['user' => $this->getUser()]);


        $favoriteMealsIds = [];
        foreach ($favoriteMeals as $favoriteMeal) {
            $favoriteMealsIds[] = sprintf("%d-%s", $favoriteMeal->getMealId()->getId(), $favoriteMeal->getMealType());
        }

        return $this->render('diet/show.html.twig', [
            'mealPlans' => $organizedMeals,
            'favoriteMealsIds' => $favoriteMealsIds,
        ]);
    }


    #[Route('/profile/diet/shopping-list', name: 'app_profile_diet_list', methods: ['GET'])]
    public function downloadShoppingList(): Response
    {
        $csvContent = $this->dietProvider->getShoppingList();

        return new Response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . 'shopping_list_' . date('Y-m-d') . '.csv' . '"',
        ]);

    }

    #[Route('/profile/info', name: 'app_profile_info')]
    public function howItWorks(): Response
    {
        return $this->render('profile/info.html.twig', );
    }
}
