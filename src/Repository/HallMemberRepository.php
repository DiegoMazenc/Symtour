<?php

namespace App\Repository;

use App\Entity\HallMember;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HallMember>
 *
 * @method HallMember|null find($id, $lockMode = null, $lockVersion = null)
 * @method HallMember|null findOneBy(array $criteria, array $orderBy = null)
 * @method HallMember[]    findAll()
 * @method HallMember[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HallMemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HallMember::class);
    }

    public function countAdmin($hall): int
    {
     return count(
         $this->createQueryBuilder('h')
             ->andWhere('h.hall = :hall')
             ->andWhere('h.status = :status')
             ->setParameter('hall', $hall)
             ->setParameter('status', "admin")
             ->getQuery()
             ->getResult()
         );
    }

//    /**
//     * @return HallMember[] Returns an array of HallMember objects
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

//    public function findOneBySomeField($value): ?HallMember
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
