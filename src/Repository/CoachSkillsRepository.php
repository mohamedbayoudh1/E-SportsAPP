<?php

namespace App\Repository;

use App\Entity\CoachSkills;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CoachSkills>
 *
 * @method CoachSkills|null find($id, $lockMode = null, $lockVersion = null)
 * @method CoachSkills|null findOneBy(array $criteria, array $orderBy = null)
 * @method CoachSkills[]    findAll()
 * @method CoachSkills[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoachSkillsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CoachSkills::class);
    }

    public function save(CoachSkills $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CoachSkills $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getSkillsOfCOach($coachId)
    {
        return $this->createQueryBuilder('cs')
            ->where('cs.coach = :coachId')
            ->setParameter('coachId', $coachId)
            ->getQuery()
            ->getResult();
    }





//    /**
//     * @return CoachSkills[] Returns an array of CoachSkills objects
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

//    public function findOneBySomeField($value): ?CoachSkills
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
