<?php

namespace App\Service;

use App\Entity\FavouriteMeal;
use App\Entity\Meals;
use App\Repository\FavouriteMealRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class FavouriteMealService
{
    /**
     * @var EntityManagerInterface
     *   The entity manager interface
     */
    protected EntityManagerInterface $entityManager;

    /**
     * @var Security
     *   The security component
     */
    protected Security $security;

    /**
     * Constructor for the FavouriteMealService.
     *
     * @param EntityManagerInterface $entityManager
     *   The entity manager interface
     * @param Security $security
     *   The security component
     */
    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    /**
     * Adds a meal to the user's favourites.
     *
     * @param UserInterface $user
     *   The user to whom the meal will be added as favourite
     * @param int $mealId
     *   The ID of the meal to be added
     * @param string $mealType
     *   The type of the meal (e.g., 'breakfast', 'lunch', etc.)
     *
     * @return array
     *   Status and message about the operation
     */
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

    /**
     * Removes a meal from the user's favourites.
     *
     * @param UserInterface $user
     *   The user from whom the meal will be removed
     * @param int $mealId
     *   The ID of the meal to be removed
     * @param string $mealType
     *   The type of the meal (e.g., 'breakfast', 'lunch', etc.)
     *
     * @return array
     *   Status and message about the operation
     */
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

    /**
     * Retrieves and groups all favourite meals by meal type for a user.
     *
     * @param FavouriteMealRepository $favouriteMealRepository
     *   The repository for favourite meals
     *
     * @return array
     *   The grouped list of favourite meals by type
     */
    public function getGroupedFavoritesByUser(FavouriteMealRepository $favouriteMealRepository): array
    {
        $userId = $this->security->getUser();
        $favourites = $favouriteMealRepository->getFavouritesByType($userId);

        $groupedFavourites = [];
        foreach ($favourites as $favourite) {
            $mealType = $favourite->getMealType();
            $mealNameJson = $favourite->getMealName();
            $mealName = json_decode($mealNameJson, true);
            $mealDetails = [
                'name' => $mealName[0]['name'],
                'calories' => $mealName[0]['calories'],
                'carbohydrates' => $mealName[0]['carbohydrates'],
                'protein' => $mealName[0]['protein'],
                'time' => $mealName[0]['time']
            ];
            $groupedFavourites[$mealType][] = [
                'mealDetails' => $mealDetails,
            ];
        }

        return $groupedFavourites;
    }
}
