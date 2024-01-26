<?php

namespace App\Repository;

use App\Entity\Band;
use App\Entity\Hall;
use App\Entity\Event;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }
    
    public function getComeEventsByHall(Hall $hall)
    {
        return $this->createQueryBuilder('e')
        ->addSelect('hall', 'band')
        ->leftJoin('e.hall', 'hall')    
        ->leftJoin('e.band', 'band')    
        ->andWhere('hall.id = :id')
        ->andWhere('e.date >= CURRENT_DATE()')
        ->setParameter('id', $hall->getId())
        ->orderBy('e.date', 'DESC')    
        ->getQuery()
        ->getResult();
    }

    public function getPastEventsByHall(Hall $hall)
    {
        return $this->createQueryBuilder('e')
        ->addSelect('hall', 'band')
        ->leftJoin('e.hall', 'hall')    
        ->leftJoin('e.band', 'band')    
        ->andWhere('hall.id = :id')
        ->andWhere('e.date < CURRENT_DATE()')
        ->setParameter('id', $hall->getId())
        ->orderBy('e.date', 'DESC')    
        ->getQuery()
        ->getResult();
    }

    public function getComeEventsByBand(Band $band)
    {
        return $this->createQueryBuilder('e')
        ->addSelect('hall', 'band')
        ->leftJoin('e.hall', 'hall')    
        ->leftJoin('e.band', 'band')    
        ->andWhere('band.id = :id')
        ->andWhere('e.date >= CURRENT_DATE()')
        ->setParameter('id', $band->getId())
        ->orderBy('e.date', 'DESC')    
        ->getQuery()
        ->getResult();
    }

    public function getPastEventsByBand(Band $band)
    {
        return $this->createQueryBuilder('e')
        ->addSelect('hall', 'band')
        ->leftJoin('e.hall', 'hall')    
        ->leftJoin('e.band', 'band')    
        ->andWhere('band.id = :id')
        ->andWhere('e.date < CURRENT_DATE()')
        ->setParameter('id', $band->getId())
        ->orderBy('e.date', 'DESC')    
        ->getQuery()
        ->getResult();
    }

    public function getBandEventGuest($hall, $date)
    {
        return $this->createQueryBuilder('e')
        ->addSelect('band')
        ->andWhere('e.hall = :id')
        ->andWhere('e.date = :date')
        ->andWhere('e.date < CURRENT_DATE()')
        ->setParameter('id', $hall->getId())
        ->setParameter('date', $date)
        ->orderBy('e.date', 'DESC')    
        ->getQuery()
        ->getResult();
    }

//    /**
//     * @return Event[] Returns an array of Event objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Event
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
