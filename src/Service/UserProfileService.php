<?php

namespace App\Service;

use App\Entity\Profile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class UserProfileService
{

    protected EntityManagerInterface $entityManager;
    protected Security $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

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
