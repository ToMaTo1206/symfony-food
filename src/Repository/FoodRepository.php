<?php

namespace App\Repository;

use App\Entity\Food;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use http\Client\Curl\User;

/**
 * @extends ServiceEntityRepository<Food>
 */
class FoodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Food::class);
    }

    public function getAllFromUser($user, ?string $search): array
    {
        $qb = $this->createQueryBuilder('f')
            ->leftJoin('f.category', 'c')
            ->andWhere('f.user = :user')
            ->setParameter('user', $user);

        if (!empty($search)) {
            $qb->andWhere('(f.name LIKE :search OR c.name LIKE :search)')
                ->setParameter('search', "%$search%");
        }
        $qb->orderBy('f.expiryDate', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function findExpiringSoon($user, int $days = 3)
    {
        $today = new \DateTimeImmutable('today');
        $limitDate = new \DateTimeImmutable("+{$days} days");

        $qb = $this->createQueryBuilder('f')
            ->andWhere('f.user = :user')
            ->setParameter('user', $user)
            ->andWhere('f.expiryDate < :limitDate')
            ->andWhere('f.expiryDate >= :today')
            ->setParameter('limitDate', $limitDate)
            ->setParameter('today', $today)
            ->orderBy('f.expiryDate', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function findExpiring($user)
    {
        $today = new \DateTimeImmutable('today');

        $qb = $this->createQueryBuilder('f')
            ->andWhere('f.user = :user')
            ->setParameter('user', $user)
            ->andWhere('f.expiryDate < :today')
            ->setParameter('today', $today)
            ->orderBy('f.expiryDate', 'ASC');

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return Food[] Returns an array of Food objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Food
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
