<?php

namespace App\Repository;

use App\Entity\MealPlan;
use App\Entity\Meals;
use App\Entity\ShoppingList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Meals>
 *
 * @method Meals|null find($id, $lockMode = null, $lockVersion = null)
 * @method Meals|null findOneBy(array $criteria, array $orderBy = null)
 * @method Meals[]    findAll()
 * @method Meals[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MealRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Meals::class);
    }

    /**
     * @return Meals[] Returns an array of Meal objects
     */
    public function findByDay($value): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.day = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findMealsForLatestWeek(): array
    {
        $latestWeekId = $this->findLatestWeek();

        return $this->getEntityManager()->createQueryBuilder()
            ->select('m')
            ->from(Meals::class, 'm')
            ->innerJoin('m.mealPlan', 'mp')
            ->where('mp.weekId = :weekId')
            ->setParameter('weekId', $latestWeekId)
            ->getQuery()
            ->getResult();
    }

    public function findLatestWeek(): bool|float|int|string
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('MAX(mp.weekId)')
            ->from(MealPlan::class, 'mp')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findShoppingList(): array {
        $latestWeekId = $this->findLatestWeek();
        return $this->getEntityManager()->createQueryBuilder()
            ->select('sl.shopping_list')
            ->from(ShoppingList::class, 'sl')
            ->innerJoin('sl.meal_plan', 'mp')
            ->where('mp.weekId = :weekId')
            ->setParameter('weekId', $latestWeekId)
            ->getQuery()
            ->getResult();
    }

}
