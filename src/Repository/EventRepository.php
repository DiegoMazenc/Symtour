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

    public function getAllEventsByHall(Hall $hall)
    {
        return $this->createQueryBuilder('e')
            ->addSelect('hall', 'bandEvents')
            ->leftJoin('e.hall', 'hall')    
            ->leftJoin('e.bandEvents', 'bandEvents')  
            ->andWhere('hall.id = :id')
            ->setParameter('id', $hall->getId())
            ->orderBy('e.date', 'DESC')    
            ->getQuery()
            ->getResult();
    }
    
    public function getComeEventsByHall(Hall $hall)
    {
        return $this->createQueryBuilder('e')
            ->addSelect('hall', 'bandEvents')
            ->leftJoin('e.hall', 'hall')    
            ->leftJoin('e.bandEvents', 'bandEvents')  
            ->andWhere('hall.id = :id')
            ->andWhere('e.date >= CURRENT_DATE()')
            ->setParameter('id', $hall->getId())
            ->orderBy('e.date', 'DESC')    
            ->getQuery()
            ->getResult();
    }

    public function getCancelEventsByHallAndDate(Hall $hall, $date, $event)
    {
        $formattedDate = (new \DateTime($date))->format('Y-m-d');

        return $this->createQueryBuilder('e')
            ->andWhere('e.hall = :hall')
            ->andWhere('e.date = :date')
            ->andWhere('e.id != :eId')
            ->setParameter('hall', $hall)
            ->setParameter('eId', $event->getId())
            ->setParameter('date', $formattedDate)
            ->orderBy('e.date', 'DESC')    
            ->getQuery()
            ->getResult();
    
    }

    public function getComeEventsByHallAsc(Hall $hall)
    {
        return $this->createQueryBuilder('e')
            ->addSelect('hall', 'bandEvents')
            ->leftJoin('e.hall', 'hall')    
            ->leftJoin('e.bandEvents', 'bandEvents')  
            ->andWhere('hall.id = :id')
            ->andWhere('e.date >= CURRENT_DATE()')
            ->setParameter('id', $hall->getId())
            ->orderBy('e.date', 'ASC')    
            ->getQuery()
            ->getResult();
    }

    public function getPastEventsByHall(Hall $hall)
    {
        return $this->createQueryBuilder('e')
        ->addSelect('hall', 'bandEvents')
        ->leftJoin('e.hall', 'hall')    
        ->leftJoin('e.bandEvents', 'bandEvents')    
        ->andWhere('hall.id = :id')
        ->andWhere('e.date < CURRENT_DATE()')
        ->setParameter('id', $hall->getId())
        ->orderBy('e.date', 'DESC')    
        ->getQuery()
        ->getResult();
    }

    public function getComeEventsByBand(Band $band)
    {
       

            $eventIds = $this->createQueryBuilder('e')
            ->select('e.id')
            ->leftJoin('e.bandEvents', 'bandEvents')  
            ->andWhere('bandEvents.band = :id')
            ->andWhere('e.date >= CURRENT_DATE()')
            ->setParameter('id', $band->getId())
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    
        $eventIds = array_column($eventIds, 'id');
    
        if (empty($eventIds)) {
            return [];
        }
    
        return $this->createQueryBuilder('e')
            ->addSelect('hall', 'bandEvents')
            ->leftJoin('e.hall', 'hall')    
            ->leftJoin('e.bandEvents', 'bandEvents')  
            ->andWhere('e.id IN (:eventIds)')
            ->setParameter('eventIds', $eventIds)
            ->orderBy('e.date', 'DESC')    
            ->getQuery()
            ->getResult();    
    }


    public function getPastEventsByBand(Band $band)
    {
       

            $eventIds = $this->createQueryBuilder('e')
            ->select('e.id')
            ->leftJoin('e.bandEvents', 'bandEvents')  
            ->andWhere('bandEvents.band = :id')
            ->andWhere('e.date < CURRENT_DATE()')
            ->setParameter('id', $band->getId())
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    
        $eventIds = array_column($eventIds, 'id');
    
        if (empty($eventIds)) {
            return [];
        }
    
        return $this->createQueryBuilder('e')
            ->addSelect('hall', 'bandEvents')
            ->leftJoin('e.hall', 'hall')    
            ->leftJoin('e.bandEvents', 'bandEvents')  
            ->andWhere('e.id IN (:eventIds)')
            ->setParameter('eventIds', $eventIds)
            ->orderBy('e.date', 'DESC')    
            ->getQuery()
            ->getResult();
    }
}
