<?php

namespace App\Repository;

use App\Entity\Band;
use App\Entity\BandMember;
use App\Entity\Profil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Profil>
 *
 * @method Profil|null find($id, $lockMode = null, $lockVersion = null)
 * @method Profil|null findOneBy(array $criteria, array $orderBy = null)
 * @method Profil[]    findAll()
 * @method Profil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Profil::class);
    }

    public function getbandMemberUser(
        Profil $profil,
    ) {
        return $this->createQueryBuilder('p')
            ->addSelect('bandMembers')
            ->addSelect('band')
            ->addSelect('role')
            ->addSelect('musicCategory')
            ->leftJoin('p.bandMembers', 'bandMembers')
            ->leftJoin('bandMembers.band', 'band')
            ->leftJoin('band.music_category ', 'musicCategory')
            ->leftJoin('bandMembers.role', 'role')
            ->andWhere('bandMembers.profil = :val')
            ->setParameter('val', $profil->getId())
            ->getQuery()
            ->getResult();
    }

    public function findBySearch($searchTerm)
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->leftJoin('p.IdUser', 'IdUser');
    
        if ($searchTerm) {
            $queryBuilder
                ->where('p.pseudo LIKE :searchTerm OR IdUser.email LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }
    
        return $queryBuilder->getQuery()->getResult();
    }
    

    //    /**
    //     * @return Profil[] Returns an array of Profil objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Profil
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
