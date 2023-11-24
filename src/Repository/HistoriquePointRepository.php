<?php

namespace App\Repository;

use App\Entity\HistoriquePoint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HistoriquePoint>
 *
 * @method HistoriquePoint|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistoriquePoint|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistoriquePoint[]    findAll()
 * @method HistoriquePoint[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoriquePointRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistoriquePoint::class);
    }

    public function save(HistoriquePoint $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(HistoriquePoint $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return flaot Returns an array of HistoriquePoint objects
     */
    public function pointpositive(): float
    {
        $result = $this->createQueryBuilder('h')
            ->select('SUM(h.point) as total')
            ->andWhere('h.point > :val')
            ->setParameter('val', 0)
            ->getQuery()
            ->getSingleResult();

        return $result['total'] ?? 0; // Retourne 0 si le résultat est null
    }
/**
     * @return flaot 
     */
    public function pointnegative(): float
    {
        return $this->createQueryBuilder('h')
        ->select('SUM(h.point)')
        ->andWhere('h.point < :val')
        ->setParameter('val', 0)
        ->getQuery()
        ->getSingleScalarResult();
    }
//    public function findOneBySomeField($value): ?HistoriquePoint
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
