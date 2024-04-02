<?php

namespace App\Service;

use App\Entity\FavouriteMeal;
use App\Entity\Meals;
use App\Entity\MealPlan;
use App\Entity\Profile;
use App\Entity\ShoppingList;
use App\Repository\MealRepository;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class DietProvider
{
    private ParameterBagInterface $parameterBag;
    private EntityManagerInterface $entityManager;
    private Client $client;
    private Security $security;
    public function __construct(ParameterBagInterface $parameterBag, EntityManagerInterface $entityManager, Security $security)
    {
        $this->parameterBag = $parameterBag;
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->client = new Client();
    }

    public function makePlan(string $prompt): ?string
    {
        $apiKey = $this->parameterBag->get('gpt_secret_key');
        $response = $this->client->post('https://api.openai.com/v1/completions', [
            'headers' => [
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'prompt' => $prompt,
                "model" => "gpt-3.5-turbo-instruct",
                "max_tokens" => 3000,
                "temperature" => 0.7,
            ],
        ]);

        if ($response->getStatusCode() == 200) {
            $body = $response->getBody();
            $data = json_decode($body->getContents(), true);

            return $data['choices'][0]['text'] ?? null;
        }

        return null;
    }

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
            if ($dayName === 'shopping_list' || $dayName == 'caloric_demand') {
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


    public function getShoppingList(): string
    {
        $shoppingList = $this->entityManager->getRepository(Meals::class)->findShoppingList();
        $items = explode("\n", $shoppingList[0]['shopping_list']);

        return implode("\r\n", $items);
    }

    public function canUserGeneratePlanThisWeek(UserInterface $user): bool {
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

    private function getStartAndEndOfWeek(): array {
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