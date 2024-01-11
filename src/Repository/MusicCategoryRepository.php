<?php

namespace App\Repository;

use App\Entity\MusicCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MusicCategory>
 *
 * @method MusicCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method MusicCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method MusicCategory[]    findAll()
 * @method MusicCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MusicCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MusicCategory::class);
    }

//    /**
//     * @return MusicCategory[] Returns an array of MusicCategory objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MusicCategory
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
