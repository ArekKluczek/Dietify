<?php

namespace App\Repository;

use App\Entity\MealPlan;
use App\Entity\Meals;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MealPlan>
 *
 * @method MealPlan|null find($id, $lockMode = null, $lockVersion = null)
 * @method MealPlan|null findOneBy(array $criteria, array $orderBy = null)
 * @method MealPlan[]    findAll()
 * @method MealPlan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MealPlanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MealPlan::class);
    }

    public function findByUserId($value): ?MealPlan
    {
        return $this->createQueryBuilder('mp')
            ->andWhere('mp.userid = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
