<?php

namespace App\Repository;

use App\Entity\Band;
use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Band>
 *
 * @method Band|null find($id, $lockMode = null, $lockVersion = null)
 * @method Band|null findOneBy(array $criteria, array $orderBy = null)
 * @method Band[]    findAll()
 * @method Band[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Band::class);
    }

    public function findBySearch($searchTerm)
    {
        $queryBuilder = $this->createQueryBuilder('b');
    
        if ($searchTerm) {
            $queryBuilder
                ->where('b.name LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }
    
        return $queryBuilder->getQuery()->getResult();
    }

    public function getBandsByHallAndDate(Event $event)
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.events', 'event')
            ->leftJoin('event.hall', 'hall')
           ->andWhere('event.date = :date')
           ->andWhere('event.band_status = :status')
           ->andWhere('hall.id = :id')
           ->setParameter('date', $event->getDate())
           ->setParameter('status', "validate")
           ->setParameter('id', $event->getHall()->getId())
           ->orderBy('b.id', 'ASC')
           ->getQuery()
           ->getResult();

    }

    public function getBandsByHallAndDateGuest(Event $event)
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.events', 'event')
            ->leftJoin('event.hall', 'hall')
           ->andWhere('event.date = :date')
           ->andWhere('event.band_status = :status')
           ->andWhere('hall.id = :id')
           ->setParameter('date', $event->getDate())
           ->setParameter('status', "guest")
           ->setParameter('id', $event->getHall()->getId())
           ->orderBy('b.id', 'ASC')
           ->getQuery()
           ->getResult();

    }
    public function getBandsByHallAndDateReject(Event $event)
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.events', 'event')
            ->leftJoin('event.hall', 'hall')
           ->andWhere('event.date = :date')
           ->andWhere('event.band_status = :status')
           ->andWhere('hall.id = :id')
           ->setParameter('date', $event->getDate())
           ->setParameter('status', "reject")
           ->setParameter('id', $event->getHall()->getId())
           ->orderBy('b.id', 'ASC')
           ->getQuery()
           ->getResult();

    }

    

//    /**
//     * @return Band[] Returns an array of Band objects
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

//    public function findOneBySomeField($value): ?Band
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
