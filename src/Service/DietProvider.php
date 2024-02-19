<?php

namespace App\Service;

use App\Entity\Meals;
use App\Entity\MealPlan;
use App\Entity\Profile;
use App\Entity\ShoppingList;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use OpenAI;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class DietProvider
{
    private ParameterBagInterface $parameterBag;
    private EntityManagerInterface $entityManager;
    private Client $client;
    public function __construct(ParameterBagInterface $parameterBag, EntityManagerInterface $entityManager)
    {
        $this->parameterBag = $parameterBag;
        $this->entityManager = $entityManager;
        $this->client = new Client();
    }

    public function makePlan(string $prompt, Profile $profile): ?string
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

        $weekId = time();
        $mealPlan = new MealPlan();
        $mealPlan->setUserid($user);
        $mealPlan->setWeekId($weekId);

        $shoppingList = new ShoppingList();
        $shoppingList->setShoppingList(implode("\n", $dietData['shopping_list']));
        $mealPlan->setShopList($shoppingList);
        $this->entityManager->persist($mealPlan);
        $this->entityManager->persist($shoppingList);

        foreach ($dietData as $dayName => $dayMeals) {
            if ($dayName === 'shopping_list') {
                continue;
            }

            $meal = new Meals();
            $meal->setMealPlan($mealPlan);
            $meal->setDayOfWeek($dayName);

            $meal->setBreakfast(json_encode($dayMeals['breakfast'] ?? null));
            $meal->setSecondBreakfast(json_encode($dayMeals['second_breakfast'] ?? null));
            $meal->setLunch(json_encode($dayMeals['lunch'] ?? null));
            $meal->setSnack(json_encode($dayMeals['snack'] ?? null));
            $meal->setDinner(json_encode($dayMeals['dinner'] ?? null));

            $this->entityManager->persist($meal);
        }

        $this->entityManager->flush();
    }


    public function getShoppingList(): array
    {
        return $this->entityManager->getRepository(Meals::class)->findShoppingList();
    }

}