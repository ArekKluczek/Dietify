<?php

namespace App\Repository;

use App\Entity\FavouriteMeal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FavouriteMeal>
 *
 * @method FavouriteMeal|null find($id, $lockMode = null, $lockVersion = null)
 * @method FavouriteMeal|null findOneBy(array $criteria, array $orderBy = null)
 * @method FavouriteMeal[]    findAll()
 * @method FavouriteMeal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FavouriteMealRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FavouriteMeal::class);
    }

    public function getFavouritesByType($userId) {
        return $this->createQueryBuilder('fm')
            ->andWhere('fm.user = :user_id')
            ->setParameter('user_id', $userId)
            ->orderBy('fm.mealType', 'ASC')
            ->getQuery()
            ->getResult();
    }

}
