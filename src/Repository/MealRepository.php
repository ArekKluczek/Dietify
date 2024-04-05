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
     *  Looking for today's meals.
     *
     * @param int $userId
     *  User id.
     * @param string $dayOfWeek
     *  Day of week.
     *
     * @return array|null
     *  Returns array of meals for actual day.
     */
    public function findDayByLatestWeek(int $userId, string $dayOfWeek): array|null
    {
        $latestWeekId = $this->findLatestWeek();

        if(!$latestWeekId) {
            return NULL;
        }

        return $this->createQueryBuilder('m')
            ->innerJoin('m.mealPlan', 'mp')
            ->where('mp.weekId = :weekId')
            ->andWhere('m.dayOfWeek = :val')
            ->andWhere('mp.userid = :userId')
            ->setParameter('val', $dayOfWeek)
            ->setParameter('weekId', $latestWeekId)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult() ?? NULL;
    }

    /**
     *  Returns meals for latest week.
     *
     * @return array
     */
    public function findMealsForLatestWeek(int $userId): array
    {
        $latestWeekId = $this->findLatestWeek();

        return $this->getEntityManager()->createQueryBuilder()
            ->select('m')
            ->from(Meals::class, 'm')
            ->innerJoin('m.mealPlan', 'mp')
            ->where('mp.weekId = :weekId')
            ->andWhere('mp.userid = :userId')
            ->setParameter('weekId', $latestWeekId)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    /**
     *  Looking for the latest week.
     *
     * @return bool|float|int|string|null
     */
    public function findLatestWeek(): bool|float|int|string|null
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('MAX(mp.weekId)')
            ->from(MealPlan::class, 'mp')
            ->getQuery()
            ->getSingleScalarResult() ?? NULL;
    }

    /**
     *  Looking for latest shopping list.
     *
     * @return array
     */
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

    /**
     *  Looking for meal by type.
     *
     * @param int $mealId
     *   Meal id.
     * @param string $mealType
     *   Meal type.
     *
     * @return array|null
     *  Returns array of meals.
     */
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
