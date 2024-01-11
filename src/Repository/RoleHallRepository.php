<?php

namespace App\Repository;

use App\Entity\RoleHall;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RoleHall>
 *
 * @method RoleHall|null find($id, $lockMode = null, $lockVersion = null)
 * @method RoleHall|null findOneBy(array $criteria, array $orderBy = null)
 * @method RoleHall[]    findAll()
 * @method RoleHall[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoleHallRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoleHall::class);
    }

//    /**
//     * @return RoleHall[] Returns an array of RoleHall objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?RoleHall
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
