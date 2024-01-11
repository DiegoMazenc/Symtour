<?php

namespace App\Repository;

use App\Entity\RoleBand;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RoleBand>
 *
 * @method RoleBand|null find($id, $lockMode = null, $lockVersion = null)
 * @method RoleBand|null findOneBy(array $criteria, array $orderBy = null)
 * @method RoleBand[]    findAll()
 * @method RoleBand[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoleBandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoleBand::class);
    }

//    /**
//     * @return RoleBand[] Returns an array of RoleBand objects
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

//    public function findOneBySomeField($value): ?RoleBand
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
