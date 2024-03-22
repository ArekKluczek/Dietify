<?php

namespace App\Service;

use App\Entity\FavouriteMeal;
use App\Entity\Meals;
use App\Entity\User;
use App\Repository\FavouriteMealRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class FavouriteMealService
{
    protected EntityManagerInterface $entityManager;
    protected Security $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function addMealToFavorites(UserInterface $user, $mealId, $mealType): array
    {
        $mealRepository = $this->entityManager->getRepository(Meals::class);
        $meal = $mealRepository->find($mealId);
        $mealName = $mealRepository->findByMealType($mealId, $mealType);

        if (!$meal) {
            return ['status' => 'error', 'message' => 'Meal not found'];
        }

        $existingFavorite = $this->entityManager->getRepository(FavouriteMeal::class)->findOneBy([
            'user' => $user,
            'meal' => $meal,
            'mealType' => $mealType
        ]);

        if ($existingFavorite) {
            return ['status' => 'error', 'message' => 'Meal already in favorites'];
        }

        $favoriteMeal = new FavouriteMeal();
        $favoriteMeal->setUserId($user);
        $favoriteMeal->setMealId($meal);
        $favoriteMeal->setMealType($mealType);

        foreach ($mealName as $key => $value) {
            $favoriteMeal->setMealType($key);
            $favoriteMeal->setMealName($value);
        }

        $this->entityManager->persist($favoriteMeal);
        $this->entityManager->flush();

        return ['status' => 'success', 'message' => 'Meal added to favorites'];
    }

    public function removeMealFromFavorites(UserInterface $user, $mealId, $mealType): array
    {
        $meal = $this->entityManager->getRepository(Meals::class)->find($mealId);

        if (!$meal) {
            return ['status' => 'error', 'message' => 'Meal not found'];
        }

        $favoriteMeal = $this->entityManager->getRepository(FavouriteMeal::class)->findOneBy([
            'user' => $user,
            'meal' => $meal,
            'mealType' => $mealType
        ]);

        if (!$favoriteMeal) {
            return ['status' => 'error', 'message' => 'Favorite meal not found'];
        }

        $this->entityManager->remove($favoriteMeal);
        $this->entityManager->flush();

        return ['status' => 'success', 'message' => 'Meal removed from favorites'];
    }

    public function getGroupedFavoritesByUser(UserInterface $user, FavouriteMealRepository $favouriteMealRepository): array
    {
        $userId = $this->security->getUser();
        $favourites = $favouriteMealRepository->getFavouritesByType($userId);

        $groupedFavourites = [];
        foreach ($favourites as $favourite) {
            $mealType = $favourite->getMealType();
            $mealNameJson = $favourite->getMealName();
            $mealName = json_decode($mealNameJson, true);
            $mealDetails = [
                'name' => $mealName['name'],
                'calories' => $mealName['calories'],
                'carbohydrates' => $mealName['carbohydrates'],
                'protein' => $mealName['protein'],
                'time' => $mealName['time']
            ];
            $groupedFavourites[$mealType][] = [
                'mealDetails' => $mealDetails,
            ];
        }

        return $groupedFavourites;
    }
}
