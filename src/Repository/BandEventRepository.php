<?php

namespace App\Repository;

use App\Entity\BandEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BandEvent>
 *
 * @method BandEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method BandEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method BandEvent[]    findAll()
 * @method BandEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BandEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BandEvent::class);
    }

//    /**
//     * @return BandEvent[] Returns an array of BandEvent objects
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

//    public function findOneBySomeField($value): ?BandEvent
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
