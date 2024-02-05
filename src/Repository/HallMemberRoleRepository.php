<?php

namespace App\Repository;

use App\Entity\HallMemberRole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HallMemberRole>
 *
 * @method HallMemberRole|null find($id, $lockMode = null, $lockVersion = null)
 * @method HallMemberRole|null findOneBy(array $criteria, array $orderBy = null)
 * @method HallMemberRole[]    findAll()
 * @method HallMemberRole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HallMemberRoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HallMemberRole::class);
    }

//    /**
//     * @return HallMemberRole[] Returns an array of HallMemberRole objects
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

//    public function findOneBySomeField($value): ?HallMemberRole
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
