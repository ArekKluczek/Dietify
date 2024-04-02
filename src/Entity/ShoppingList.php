<?php

namespace App\Entity;

use App\Repository\ShoppingListRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShoppingListRepository::class)]
class ShoppingList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 2048, nullable: true)]
    private ?string $shopping_list = null;

    #[ORM\OneToOne(inversedBy: 'shopList', cascade: ['persist', 'remove'])]
    private ?MealPlan $meal_plan = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShoppingList(): ?string
    {
        return $this->shopping_list;
    }

    public function setShoppingList(?string $shopping_list): static
    {
        $this->shopping_list = $shopping_list;

        return $this;
    }

    public function getMealPlan(): ?MealPlan
    {
        return $this->meal_plan;
    }

    public function setMealPlan(?MealPlan $meal_plan): static
    {
        $this->meal_plan = $meal_plan;

        return $this;
    }
}
