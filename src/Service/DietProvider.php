<?php

namespace App\Service;

use App\Entity\MealPlan;
use App\Entity\Meals;
use App\Entity\Profile;
use App\Entity\ShoppingList;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class DietProvider
{
    /**
     * @var ParameterBagInterface
     *   The interface for accessing parameters.
     */
    private ParameterBagInterface $parameterBag;

    /**
     * @var EntityManagerInterface
     *   The entity manager interface for interacting with the ORM.
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var Client
     *   The HTTP client for making requests.
     */
    private Client $client;

    /**
     * Constructor for the DietProvider service.
     *
     * @param ParameterBagInterface $parameterBag
     *   The parameter bag.
     * @param EntityManagerInterface $entityManager
     *   The entity manager.
     * @param Security $security
     *   The security.
     */
    public function __construct(ParameterBagInterface $parameterBag, EntityManagerInterface $entityManager)
    {
        $this->parameterBag = $parameterBag;
        $this->entityManager = $entityManager;
        $this->client = new Client();
    }

    /**
     * Creates a diet plan by making a request to an external API.
     *
     * @param string $prompt
     *   The input prompt for generating the diet plan.
     * @return string|null
     *   The generated diet plan or null if the request fails.
     */
    public function makePlan(string $prompt): ?string
    {
        $apiKey = $this->parameterBag->get('gpt_secret_key');
        $response = $this->client->post('https://api.openai.com/chat/completions', [
            'headers' => [
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'prompt' => $prompt,
                "model" => "gpt-3.5-turbo-instruct",
                "max_tokens" => 3500,
                "temperature" => 1,
            ],
        ]);

        if ($response->getStatusCode() == 200) {
            $body = $response->getBody();
            $data = json_decode($body->getContents(), true);

            return $data['choices'][0]['text'] ?? null;
        }

        return null;
    }

    /**
     * Processes the generated diet plan, creates meal entities, and persists them in the database.
     *
     * @param string $jsonResponse
     *   The JSON response from the diet generation API.
     * @param UserInterface $user
     *   The user for whom the diet plan is being created.
     */
    public function makeDiet(string $jsonResponse, UserInterface $user): void
    {
        $jsonResponse = preg_replace('/(\d+)g/', '$1', $jsonResponse);
        $jsonResponse = str_replace("'", '"', $jsonResponse);
        $jsonResponse = trim($jsonResponse);
        $dietData = json_decode($jsonResponse, true);
        $profile = $this->entityManager->getRepository(Profile::class)->findByUserId($user->getId());

        $weekId = time();
        $mealPlan = new MealPlan();
        $mealPlan->setUserid($user);
        $mealPlan->setWeekId($weekId);

        $shoppingList = new ShoppingList();
        $shoppingList->setShoppingList(implode("\n", $dietData['shopping_list']));
        $mealPlan->setShopList($shoppingList);
        $profile->setCaloricdemand($dietData['caloric_demand']);
        $this->entityManager->persist($mealPlan);
        $this->entityManager->persist($shoppingList);

        foreach ($dietData as $dayName => $dayMeals) {
            $allowedDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

            if (!in_array(strtolower($dayName), $allowedDays)) {
                continue;
            }

            $meal = new Meals();
            $meal->setMealPlan($mealPlan);
            $meal->setDayOfWeek(strtolower($dayName));

            $meal->setBreakfast(json_encode($dayMeals['breakfast'] ?? null));
            $meal->setBrunch(json_encode($dayMeals['brunch'] ?? null));
            $meal->setLunch(json_encode($dayMeals['lunch'] ?? null));
            $meal->setSnack(json_encode($dayMeals['snack'] ?? null));
            $meal->setDinner(json_encode($dayMeals['dinner'] ?? null));

            $this->entityManager->persist($meal);
        }

        $this->entityManager->flush();
    }

    /**
     * Retrieves a shopping list for the latest meal plan.
     *
     * @return string
     *   The shopping list in a string format, ready for download.
     */
    public function getShoppingList(): string
    {
        $shoppingList = $this->entityManager->getRepository(Meals::class)->findShoppingList();
        $items = explode("\n", $shoppingList[0]['shopping_list']);

        return implode("\r\n", $items);
    }

    /**
     * Determines whether the user can generate a new meal plan for the current week.
     *
     * @param UserInterface $user
     *   The user in question.
     *
     * @return bool
     *   True if the user can generate a new plan, false otherwise.
     */
    public function canUserGeneratePlanThisWeek(UserInterface $user): bool
    {
        $weekTimestamps = $this->getStartAndEndOfWeek();
        $qb = $this->entityManager->getRepository(MealPlan::class)->createQueryBuilder('mp');

        $qb->where('mp.userid = :user')
            ->andWhere('mp.weekId >= :startOfWeek')
            ->andWhere('mp.weekId <= :endOfWeek')
            ->setParameter('user', $user)
            ->setParameter('startOfWeek', $weekTimestamps['start'])
            ->setParameter('endOfWeek', $weekTimestamps['end']);

        $existingPlan = $qb->getQuery()->getResult();

        return count($existingPlan) === 0;
    }

    /**
     * Calculates the start and end timestamps of the current week.
     *
     * @return array
     *   An array containing the start and end timestamps.
     */
    private function getStartAndEndOfWeek(): array
    {
        $today = new \DateTime();
        $startOfWeek = clone $today;
        $endOfWeek = clone $today;

        $startOfWeek->modify('this week')->setTime(0, 0, 0);

        $endOfWeek->modify('this week +6 days')->setTime(23, 59, 59);

        return [
            'start' => $startOfWeek->getTimestamp(),
            'end' => $endOfWeek->getTimestamp(),
        ];
    }

    /**
     * Organizes meal data by encoding it into JSON and appending a unique identifier.
     *
     * @param mixed $meal
     *   The meal entity.
     * @param int $mealId
     *   The ID of the meal.
     *
     * @return array
     *   An array containing the organized meal data.
     */
    public function organizeMealData($meal, $mealId): array
    {
        return [
            'breakfast' => array_merge(json_decode($meal->getBreakfast(), true), ['uniqueMealId' => $mealId . '-breakfast']),
            'brunch' => array_merge(json_decode($meal->getBrunch(), true), ['uniqueMealId' => $mealId . '-brunch']),
            'lunch' => array_merge(json_decode($meal->getLunch(), true), ['uniqueMealId' => $mealId . '-lunch']),
            'snack' => array_merge(json_decode($meal->getSnack(), true), ['uniqueMealId' => $mealId . '-snack']),
            'dinner' => array_merge(json_decode($meal->getDinner(), true), ['uniqueMealId' => $mealId . '-dinner']),
        ];
    }
}