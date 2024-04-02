<?php

namespace App\Service;

use App\Entity\Profile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class UserProfileService
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
     * Constructor for the UserProfileService.
     *
     * @param EntityManagerInterface $entityManager
     *   The entity manager.
     * @param Security $security
     *   The security.
     */
    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    /**
     * Calculates the Body Mass Index (BMI) for a given user profile.
     *
     * @param Profile $profile
     *   The profile entity containing user's physical data.
     *
     * @return array|null
     *   Returns an array with the BMI value and category or null if the user is not found.
     */
    public function calculateBMI(Profile $profile): ?array
    {
        $user = $this->security->getUser();

        if ($user) {
            $height = $profile->getHeight() / 100;
            $weight = $profile->getWeight();
            $bmiValue = $weight / ($height ** 2);
            $category = $this->printBMI($bmiValue);
            return [
                'value' => $bmiValue,
                'category' => $category
            ];
        }

        return null;
    }

    /**
     * Determines the BMI category based on the BMI value.
     *
     * @param float $bmi
     *   The BMI value to categorize.
     *
     * @return string
     *   Returns the category string for the BMI value.
     */
    private function printBMI(float $bmi): string
    {
        return match (true) {
            $bmi < 16 => 'Severely underweight',
            $bmi >= 16 && $bmi < 18.5 => 'Underweight',
            $bmi >= 18.5 && $bmi < 25 => 'Normal (healthy weight)',
            $bmi >= 25 && $bmi < 30 => 'Overweight',
            $bmi >= 30 && $bmi < 35 => 'Obese Class I (Moderately obese)',
            $bmi >= 35 && $bmi < 40 => 'Obese Class II (Severely obese)',
            default => 'Obese Class III (Very severely obese)',
        };
    }

    /**
     * Collects diet-related data from the user's profile.
     *
     * @param Profile $profile
     *   The profile entity from which to extract the data.
     *
     * @return array
     *   Returns an array of diet preference data.
     */
    public function getDietPromptData($profile): array {
        return [
            'height' => $profile->getHeight(),
            'weight' => $profile->getWeight(),
            'age' => $profile->getAge(),
            'gender' => $profile->getGender(),
            'dietPreferences' => $profile->getDietpreferences(),
            'allergies' => $profile->getAllergies(),
            'activity' => $profile->getActivityLevel(),
        ];
    }

}
