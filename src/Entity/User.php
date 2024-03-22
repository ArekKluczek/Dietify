<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $secondPassword = null;

    #[ORM\OneToMany(mappedBy: 'userid', targetEntity: MealPlan::class, cascade: ['persist', 'remove'])]
    private Collection $mealPlans;

    #[ORM\OneToOne(mappedBy: 'userid', targetEntity: Profile::class, cascade: ['persist', 'remove'])]
    private ?Profile $profile = null;

    #[ORM\OneToMany(mappedBy: 'user_id', targetEntity: FavouriteMeal::class)]
    private Collection $favouriteMeals;

    #[Pure]
    public function __construct() {
        $this->mealPlans = new ArrayCollection();
        $this->favouriteMeals = new ArrayCollection();
    }

    public function getDiets(): Collection {
        return $this->mealPlans;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getSecondPassword(): string
    {
        return $this->secondPassword;
    }

    public function setSecondPassword(string $secondPassword): static
    {
        $this->secondPassword = $secondPassword;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, FavouriteMeal>
     */
    public function getFavouriteMeals(): Collection
    {
        return $this->favouriteMeals;
    }

    public function addFavouriteMeal(FavouriteMeal $favouriteMeal): static
    {
        if (!$this->favouriteMeals->contains($favouriteMeal)) {
            $this->favouriteMeals->add($favouriteMeal);
            $favouriteMeal->setUserId($this);
        }

        return $this;
    }

    public function removeFavouriteMeal(FavouriteMeal $favouriteMeal): static
    {
        if ($this->favouriteMeals->removeElement($favouriteMeal)) {
            // set the owning side to null (unless already changed)
            if ($favouriteMeal->getUserId() === $this) {
                $favouriteMeal->setUserId(null);
            }
        }

        return $this;
    }
}
