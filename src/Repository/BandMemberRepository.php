<?php

namespace App\Repository;

use App\Entity\BandMember;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BandMember>
 *
 * @method BandMember|null find($id, $lockMode = null, $lockVersion = null)
 * @method BandMember|null findOneBy(array $criteria, array $orderBy = null)
 * @method BandMember[]    findAll()
 * @method BandMember[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BandMemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BandMember::class);
    }

//    /**
//     * @return BandMember[] Returns an array of BandMember objects
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

//    public function findOneBySomeField($value): ?BandMember
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
