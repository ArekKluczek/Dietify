<?php

namespace App\Entity;

use App\Repository\FavouriteMealRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: FavouriteMealRepository::class)]
class FavouriteMeal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "string")]
    private ?string $mealName = null;

    #[ORM\Column(type: "string")]
    private ?string $mealType = null;

    #[ORM\ManyToOne(inversedBy: 'favouriteMeals')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'favouriteMeals')]
    private ?Meals $meal = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getMealName(): ?string
    {
        return $this->mealName;
    }

    /**
     * @param string|null $mealName
     */
    public function setMealName(?string $mealName): void
    {
        $this->mealName = $mealName;
    }

    /**
     * @return string|null
     */
    public function getMealType(): ?string
    {
        return $this->mealType;
    }

    /**
     * @param string|null $mealType
     */
    public function setMealType(?string $mealType): void
    {
        $this->mealType = $mealType;
    }

    public function getUserId(): ?User
    {
        return $this->user;
    }

    public function setUserId(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getMealId(): ?Meals
    {
        return $this->meal;
    }

    public function setMealId(?Meals $meal): static
    {
        $this->meal = $meal;

        return $this;
    }
}
