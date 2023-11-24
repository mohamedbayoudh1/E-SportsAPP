<?php

namespace App\Repository;

use App\Entity\Cours;
use App\Entity\Jeux;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cours>
 *
 * @method Cours|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cours|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cours[]    findAll()
 * @method Cours[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cours::class);
    }

    public function save(Cours $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Cours $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findCoursesByCoachId($coachId)
    {
        return $this->createQueryBuilder('c')
            ->where('c.idCoach = :coachId')
            ->setParameter('coachId', $coachId)
            ->getQuery()
            ->getResult();
    }
    public function findCoursesByCoachIdEtat($coachId, $etat)
    {
        return $this->createQueryBuilder('c')
            ->where('c.idCoach = :coachId')
            ->andWhere('c.etat = :etat')
            ->setParameter('coachId', $coachId)
            ->setParameter('etat', $etat)
            ->getQuery()
            ->getResult();
    }

    public function profileCoachEtat1($coachId)
    {
        return $this->createQueryBuilder('c')
            ->where('c.idCoach = :coachId')
            ->andWhere('c.etat = :etat')
            ->setParameter('coachId', $coachId)
            ->setParameter('etat', 1)
            ->getQuery()
            ->getResult();
    }

    public function findByGame(int $gameId)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.idJeux = :gameId')
            ->setParameter('gameId', $gameId)
            ->getQuery()
            ->getResult();
    }

    public function findByRankAndGame($rank, $game)
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.idJeux', 'j')
            ->andWhere('j.nomGame = :game')
            ->setParameter('game', $game);

        switch ($rank) {
            case 'IRON':
            case 'BRONZE':
            case 'SILVER':
                $qb->andWhere('c.niveau = :debutant')
                    ->setParameter('debutant', 'Débutant');
                break;
            case 'GOLD':
            case 'PLATINUM':
            case 'DIAMOND':
                $qb->andWhere('c.niveau = :intermediaire')
                    ->setParameter('intermediaire', 'Intermédiaire');
                break;
            case 'MASTER':
            case 'GRANDMASTER':
            case 'CHALLENGER':
                $qb->andWhere('c.niveau = :avance')
                    ->setParameter('avance', 'Avancé');
                break;
            default:
                // Do nothing
                break;
        }

        return $qb->getQuery()->getResult();
    }

    public function findBynomGame($game)
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.idJeux', 'j')
            ->andWhere('j.nomGame = :game')
            ->setParameter('game', $game);
        return $qb->getQuery()->getResult();
    }




    //    /**
    //     * @return Cours[] Returns an array of Cours objects
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

    //    public function findOneBySomeField($value): ?Cours
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
