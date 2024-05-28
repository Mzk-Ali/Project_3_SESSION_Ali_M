<?php

namespace App\Repository;

use App\Entity\Session;
use App\Entity\Stagiaire;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Stagiaire>
 */
class StagiaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stagiaire::class);
    }

    //    /**
    //     * @return Stagiaire[] Returns an array of Stagiaire objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Stagiaire
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    
    public function findStagiairesUnregistered(Session $session): array
    {

        $qb = $this->createQueryBuilder('st');

        // Sous-requête
        $subQuery = $this->createQueryBuilder('st2')
            ->select('st2.id')
            ->innerJoin('st2.sessions', 's')
            ->where('s.id = :session_id')
            ->getDQL();

        $qb->where($qb->expr()->notIn('st.id', $subQuery))
           ->setParameter('session_id', $session->getId());

        return $qb->getQuery()->getResult();
    }
}
