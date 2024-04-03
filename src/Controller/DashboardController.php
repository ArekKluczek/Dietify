<?php

namespace App\Controller;

use App\Entity\Meals;
use App\Repository\MealRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = $security->getUser();
        if ($user) {
            $userId = $user->getId();

            $meal = $entityManager->getRepository(Meals::class)->findDayByLatestWeek($userId, date('l', strtotime(gmdate('Y-m-d', time()))));
            if ($meal) {
                $mealId = $meal[0]->getId();

                $organizedMeal[] = [
                    'breakfast' => array_merge(json_decode($meal[0]->getBreakfast(), true), ['uniqueMealId' => $mealId . '-breakfast']),
                    'brunch' => array_merge(json_decode($meal[0]->getBrunch(), true), ['uniqueMealId' => $mealId . '-brunch']),
                    'lunch' => array_merge(json_decode($meal[0]->getLunch(), true), ['uniqueMealId' => $mealId . '-lunch']),
                    'snack' => array_merge(json_decode($meal[0]->getSnack(), true), ['uniqueMealId' => $mealId . '-snack']),
                    'dinner' => array_merge(json_decode($meal[0]->getDinner(), true), ['uniqueMealId' => $mealId . '-dinner']),
                ];

                return $this->render('dashboard/index.html.twig', [
                    'meals' => $organizedMeal,
                ]);
            }
        }

        return $this->render('dashboard/index.html.twig', [
            'meals' => NULL,
        ]);

    }

    #[Route('/how-it-works', name: 'app_dashboard_how_it_works')]
    public function howItWorks(): Response
    {
        return $this->render('dashboard/how-it-works.html.twig', );
    }
}
