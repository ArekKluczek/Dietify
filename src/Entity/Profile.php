<?php

namespace App\Entity;

use App\Repository\ProfileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProfileRepository::class)]
class Profile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'profile', targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userid = null;

    #[ORM\Column]
    private ?float $weight = null;

    #[ORM\Column]
    private ?float $height = null;

    #[ORM\Column]
    private ?int $age = null;

    #[ORM\Column]
    private ?string $gender = null;

    #[ORM\Column]
    private ?string $activitylevel = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dietpreferences = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $allergies = null;

    #[ORM\Column(nullable: true)]
    private ?float $calorieresrequirement = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserid(): User
    {
        return $this->userid;
    }

    public function setUserid(User $userid): static
    {
        $this->userid = $userid;

        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function setHeight(float $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(bool $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getActivitylevel(): ?string
    {
        return $this->activitylevel;
    }

    public function setActivitylevel(string $activitylevel): static
    {
        $this->activitylevel = $activitylevel;

        return $this;
    }

    public function getDietpreferences(): ?string
    {
        return $this->dietpreferences;
    }

    public function setDietpreferences(?string $dietpreferences): static
    {
        $this->dietpreferences = $dietpreferences;

        return $this;
    }

    public function getAllergies(): ?string
    {
        return $this->allergies;
    }

    public function setAllergies(?string $allergies): static
    {
        $this->allergies = $allergies;

        return $this;
    }

    public function getCalorieresrequirement(): ?float
    {
        return $this->calorieresrequirement;
    }

    public function setCalorieresrequirement(?float $calorieresrequirement): static
    {
        $this->calorieresrequirement = $calorieresrequirement;

        return $this;
    }
}
