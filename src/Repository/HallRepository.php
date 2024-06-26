<?php

namespace App\Repository;

use App\Entity\Band;
use App\Entity\Hall;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Hall>
 *
 * @method Hall|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hall|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hall[]    findAll()
 * @method Hall[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HallRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hall::class);
    }

    public function filter(array $data)
    {
        $qb = $this->createQueryBuilder('h');
        if (!empty($data['musicCategory'])) {
            $qb
                ->leftJoin('h.music_category', 'musicCategory')
                ->andWhere('musicCategory.category = :category')
                ->setParameter('category', $data['musicCategory']);
        }
    
        if (!empty($data['city'])) {
            $qb
                ->leftJoin('h.hallInfo', 'hallInfo')
                ->andWhere('hallInfo.city = :city')
                ->setParameter('city', $data['city']);
        }

        if (!empty($data['date'])) {
            // Utilisez une sous-requête pour vérifier l'absence d'événements à la date spécifiée
            $qb
                ->andWhere('(NOT EXISTS (
                    SELECT 1
                    FROM App\Entity\Event e
                    WHERE e.hall = h
                    AND e.date = :date
                ))')
                ->setParameter('date', $data['date']);
        }
        
        return $qb->getQuery()->getResult();
    }


    public function findBySearch($searchTerm)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.name LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $searchTerm . '%')
            ->getQuery()
            ->getResult();
    }


   public function findByCompleteInfo()
    {
        return $this->createQueryBuilder('h')
            ->leftJoin('h.hallInfo', 'info')
            ->andWhere('info.zip_code IS NOT NULL')
            ->getQuery()
            ->getResult();
    }
   



    
//    /**
//     * @return Hall[] Returns an array of Hall objects
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

//    public function findOneBySomeField($value): ?Hall
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
