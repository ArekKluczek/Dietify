<?php

namespace App\Entity;

use App\Repository\MealPlanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: MealPlanRepository::class)]
class MealPlan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $totalCalories = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'diets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userid = null;

    #[ORM\OneToMany(mappedBy: 'meals', targetEntity: Meals::class)]
    private Collection $meals;

    #[ORM\Column]
    private ?int $weekId = null;

    public function __construct()
    {
        $this->meals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalCalories(): ?int
    {
        return $this->totalCalories;
    }

    public function setTotalCalories(?int $totalCalories): static
    {
        $this->totalCalories = $totalCalories;

        return $this;
    }

    /**
     * @return Collection<int, Meals>
     */
    public function getMeals(): Collection
    {
        return $this->meals;
    }

    public function addMeals(Meals $meals): static
    {
        if (!$this->meals->contains($meals)) {
            $this->meals->add($meals);
            $meals->setMealPlan($this);
        }

        return $this;
    }

    public function removeMeals(Meals $meals): static
    {
        if ($this->meals->removeElement($meals)) {
            // set the owning side to null (unless already changed)
            if ($meals->getMealPlan() === $this) {
                $meals->setMealPlan(null);
            }
        }

        return $this;
    }

    public function getUserid(): ?UserInterface
    {
        return $this->userid;
    }

    public function setUserid(?UserInterface $userid): static
    {
        $this->userid = $userid;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getWeekId(): ?int
    {
        return $this->weekId;
    }

    /**
     * @param int|null $weekId
     */
    public function setWeekId(?int $weekId): void
    {
        $this->weekId = $weekId;
    }

    public function getShopList(): ?ShoppingList
    {
        return $this->shopList;
    }

    public function setShopList(?ShoppingList $shopList): static
    {
        // unset the owning side of the relation if necessary
        if ($shopList === null && $this->shopList !== null) {
            $this->shopList->setMealPlan(null);
        }

        // set the owning side of the relation if necessary
        if ($shopList !== null && $shopList->getMealPlan() !== $this) {
            $shopList->setMealPlan($this);
        }

        $this->shopList = $shopList;

        return $this;
    }

}
