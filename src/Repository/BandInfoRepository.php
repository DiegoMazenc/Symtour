<?php

namespace App\Repository;

use App\Entity\BandInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BandInfo>
 *
 * @method BandInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method BandInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method BandInfo[]    findAll()
 * @method BandInfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BandInfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BandInfo::class);
    }

//    /**
//     * @return BandInfo[] Returns an array of BandInfo objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?BandInfo
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
