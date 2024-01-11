<?php

namespace App\Repository;

use App\Entity\HallInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HallInfo>
 *
 * @method HallInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method HallInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method HallInfo[]    findAll()
 * @method HallInfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HallInfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HallInfo::class);
    }

//    /**
//     * @return HallInfo[] Returns an array of HallInfo objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?HallInfo
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
