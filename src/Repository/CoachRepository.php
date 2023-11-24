<?php

namespace App\Repository;

use App\Entity\Coach;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Coach>
 *
 * @method Coach|null find($id, $lockMode = null, $lockVersion = null)
 * @method Coach|null findOneBy(array $criteria, array $orderBy = null)
 * @method Coach[]    findAll()
 * @method Coach[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoachRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Coach::class);
    }

    public function save(Coach $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Coach $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function getCoach($coachId)
    {
        return $this->createQueryBuilder('c')
            ->where('c.id = :coachId')
            ->setParameter('coachId', $coachId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Coach[] Returns an array of Coach objects
     */
    public function finddemande($value,$value2): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.approuver = :val')
            ->andWhere('c.bannir = :val2')
            ->setParameter('val', $value)
            ->setParameter('val2', $value2)
            ->getQuery()
            ->getResult()
        ;
    }


    /**
     * @return Coach[] Returns an array of Coach objects
     */
    public function findactive($value,$value2): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.approuver = :val')
            ->andWhere('c.bannir = :val2')
            ->setParameter('val', $value)
            ->setParameter('val2', $value2)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Coach[] Returns an array of Coach objects
     */
    public function findByban($value): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.bannir = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?Coach
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
