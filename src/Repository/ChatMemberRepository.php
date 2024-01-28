<?php

namespace App\Repository;

use App\Entity\ChatMember;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChatMember>
 *
 * @method ChatMember|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChatMember|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChatMember[]    findAll()
 * @method ChatMember[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChatMemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChatMember::class);
    }

//    /**
//     * @return ChatMember[] Returns an array of ChatMember objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ChatMember
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
