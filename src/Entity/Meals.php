<?php

namespace App\Entity;

use App\Repository\MealRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MealRepository::class)]
class Meals
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: MealPlan::class, inversedBy: 'meals')]
    private ?MealPlan $mealPlan = null;

    #[ORM\Column(type: "string")]
    private ?string $dayOfWeek = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?string $breakfast = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?string $secondBreakfast = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?string $lunch = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?string $snack = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?string $dinner = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMealPlan(): ?MealPlan
    {
        return $this->mealPlan;
    }

    public function setMealPlan(?MealPlan $mealPlan): static
    {
        $this->mealPlan = $mealPlan;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDayOfWeek(): ?string
    {
        return $this->dayOfWeek;
    }

    /**
     * @param string|null $dayOfWeek
     */
    public function setDayOfWeek(?string $dayOfWeek): void
    {
        $this->dayOfWeek = $dayOfWeek;
    }

    /**
     * @return string|null
     */
    public function getBreakfast(): ?string
    {
        return $this->breakfast;
    }

    /**
     * @param string|null $breakfast
     */
    public function setBreakfast(?string $breakfast): void
    {
        $this->breakfast = $breakfast;
    }

    /**
     * @return string|null
     */
    public function getSecondBreakfast(): ?string
    {
        return $this->secondBreakfast;
    }

    /**
     * @param string|null $secondBreakfast
     */
    public function setSecondBreakfast(?string $secondBreakfast): void
    {
        $this->secondBreakfast = $secondBreakfast;
    }

    /**
     * @return string|null
     */
    public function getLunch(): ?string
    {
        return $this->lunch;
    }

    /**
     * @param string|null $lunch
     */
    public function setLunch(?string $lunch): void
    {
        $this->lunch = $lunch;
    }

    /**
     * @return string|null
     */
    public function getSnack(): ?string
    {
        return $this->snack;
    }

    /**
     * @param string|null $snack
     */
    public function setSnack(?string $snack): void
    {
        $this->snack = $snack;
    }

    /**
     * @return string|null
     */
    public function getDinner(): ?string
    {
        return $this->dinner;
    }

    /**
     * @param string|null $dinner
     */
    public function setDinner(?string $dinner): void
    {
        $this->dinner = $dinner;
    }

}
