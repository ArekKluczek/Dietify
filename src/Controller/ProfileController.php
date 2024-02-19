<?php

namespace App\Controller;

use App\Entity\MealPlan;
use App\Entity\Meals;
use App\Entity\Profile;
use App\Entity\User;
use App\Form\ProfileType;
use App\Service\BMICalculatorService;
use App\Service\DietProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    private BMICalculatorService $calculatorService;
    private DietProvider $dietProvider;
    private EntityManagerInterface $entityManager;
    private Security $security;

    public function __construct(BMICalculatorService $calculatorService, DietProvider $dietProvider, EntityManagerInterface $entityManager, Security $security)
    {
        $this->calculatorService = $calculatorService;
        $this->dietProvider = $dietProvider;
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    #[Route('/profile', name: 'app_profile')]
    public function index(Request $request): Response
    {
        $profile = new Profile();
        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->entityManager->getRepository(User::class)->find($this->security->getUser()->getId());
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
        $profile = $this->entityManager->getRepository(Profile::class)->find($this->security->getUser()->getId());
        $bmi = $this->calculatorService->calculateBMI($profile);

        return $this->render('profile/profile.html.twig', [
            'bmi' => $bmi,
        ]);
    }

    #[Route('/profile/diet', name: 'app_profile_diet', methods: ['GET', 'POST'])]
    public function generate(): Response
    {
        $user = $this->security->getUser();
        $profile = $this->entityManager->getRepository(Profile::class)->findByUserId($user?->getId());
        $weight = $profile->getWeight();
        $height = $profile->getHeight();
        $activity = $profile->getActivitylevel();
        $gender = $profile->getGender();
        $dietPreferences = $profile->getDietpreferences();
        $allergies = $profile->getAllergies();
        $age = $profile->getAge();

        $prompt = "
        Please generate a weekly meal plan in JSON format for a person with the following parameters: height {$height}cm, weight {$weight}kg, age {$age}years, $gender, activity $activity. The plan should include 5 days, starting from 'monday' to 'friday', with 5 meals each day: 'breakfast', 'second_breakfast', 'lunch', 'snack', and 'dinner'. Each meal entry should specify the meal's name, calories, carbohydrates, protein content, and the recommended time for consumption.
        Please fill in the rest of the week with varied meals that fit a balanced diet and provide a complete shopping list for the entire week in a separate field named 'shopping_list'.
        ";
        $generatedText = $this->dietProvider->makePlan($prompt, $profile);
        $this->dietProvider->makeDiet($generatedText, $user);
        return $this->redirectToRoute('app_profile_diet_show');
    }

    #[Route('/profile/diet/show', name: 'app_profile_diet_show', methods: ['GET'])]
    public function showDiet(): Response
    {
        $meals = $this->entityManager->getRepository(Meals::class)->findMealsForLatestWeek();
        $organizedMeals = [];
        foreach ($meals as $meal) {
            $day = $meal->getDayOfWeek();

            $organizedMeals[$day][] = [
                'breakfast' => json_decode($meal->getBreakfast(), true),
                'secondBreakfast' => json_decode($meal->getSecondBreakfast(), true),
                'lunch' => json_decode($meal->getLunch(), true),
                'snack' => json_decode($meal->getSnack(), true),
                'dinner' => json_decode($meal->getDinner(), true),
            ];
        }

        return $this->render('diet/show.html.twig', [
            'mealPlans' => $organizedMeals,
        ]);
    }

    #[Route('/profile/diet/shopping-list', name: 'app_profile_diet_list', methods: ['GET'])]
    public function downloadShoppingList(): Response
    {
        $csvFileName = 'shopping_list_' . date('Y-m-d') . '.csv';
        $shoppingList = $this->dietProvider->getShoppingList();
        $items = explode("\n", $shoppingList[0]['shopping_list']);

        $csvContent = implode("\r\n", $items);

        return new Response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
        ]);

    }


}
