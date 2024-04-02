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
    public function findDayByLatestWeek(string $value): array|null
    {
        $latestWeekId = $this->findLatestWeek();

        if(!$latestWeekId) {
            return NULL;
        }

        return $this->createQueryBuilder('m')
            ->innerJoin('m.mealPlan', 'mp')
            ->where('mp.weekId = :weekId')
            ->andWhere('m.dayOfWeek = :val')
            ->setParameter('val', $value)
            ->setParameter('weekId', $latestWeekId)
            ->getQuery()
            ->getResult() ?? NULL;
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

    public function findLatestWeek(): bool|float|int|string|null
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('MAX(mp.weekId)')
            ->from(MealPlan::class, 'mp')
            ->getQuery()
            ->getSingleScalarResult() ?? NULL;
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


    public function findByMealType(int $mealId, string $mealType): ?array {
        $allowedMealTypes = ['breakfast', 'brunch', 'lunch', 'snack', 'dinner'];
        if (!in_array($mealType, $allowedMealTypes)) {
            throw new \InvalidArgumentException('Invalid meal type');
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('m.' . $mealType)
            ->from('App\Entity\Meals', 'm')
            ->where('m.id = :mealId')
            ->setParameter('mealId', $mealId);

        $query = $qb->getQuery();
        return $query->getOneOrNullResult();
    }

}
