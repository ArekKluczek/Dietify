<?php

namespace App\Controller;

use App\Repository\FavouriteMealRepository;
use App\Service\FavouriteMealService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FavouriteMealController extends AbstractController
{
    private FavouriteMealService $favouriteMealService;

    public function __construct(FavouriteMealService $favouriteMealService)
    {
        $this->favouriteMealService = $favouriteMealService;
    }

    #[Route('/api/add-to-favorites/{mealType}/{mealId}', name: 'add_to_favorites', methods: ['POST'])]
    public function addToFavorites($mealType, $mealId): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['status' => 'error', 'message' => 'User not authenticated'], 401);
        }

        $result = $this->favouriteMealService->addMealToFavorites($user, $mealId, $mealType);
        return new JsonResponse($result, $result['status'] === 'success' ? 200 : 404);
    }

    #[Route('/api/remove-from-favorites/{mealType}/{mealId}', name: 'remove_from_favorites', methods: ['POST'])]
    public function removeFromFavorites($mealType, $mealId): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['status' => 'error', 'message' => 'User not authenticated'], 401);
        }

        $result = $this->favouriteMealService->removeMealFromFavorites($user, $mealId, $mealType);
        return new JsonResponse($result, $result['status'] === 'success' ? 200 : 404);
    }

    #[Route('/api/favourites', name: 'app_favourites', methods: ['GET'])]
    public function index(FavouriteMealRepository $favouriteMealRepository): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['status' => 'error', 'message' => 'User not authenticated'], 401);
        }

        $groupedFavourites = $this->favouriteMealService->getGroupedFavoritesByUser($favouriteMealRepository);

        return new JsonResponse($groupedFavourites);
    }
}