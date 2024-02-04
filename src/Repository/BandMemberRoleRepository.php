<?php

namespace App\Repository;

use App\Entity\BandMemberRole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BandMemberRole>
 *
 * @method BandMemberRole|null find($id, $lockMode = null, $lockVersion = null)
 * @method BandMemberRole|null findOneBy(array $criteria, array $orderBy = null)
 * @method BandMemberRole[]    findAll()
 * @method BandMemberRole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BandMemberRoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BandMemberRole::class);
    }

//    /**
//     * @return BandMemberRole[] Returns an array of BandMemberRole objects
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

//    public function findOneBySomeField($value): ?BandMemberRole
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
