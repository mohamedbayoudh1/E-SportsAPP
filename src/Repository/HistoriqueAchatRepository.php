<?php

namespace App\Repository;

use App\Entity\HistoriqueAchat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HistoriqueAchat>
 *
 * @method HistoriqueAchat|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistoriqueAchat|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistoriqueAchat[]    findAll()
 * @method HistoriqueAchat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoriqueAchatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistoriqueAchat::class);
    }

    public function save(HistoriqueAchat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(HistoriqueAchat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findachatByGamerIdEtat($gamerId)
    {
        return $this->createQueryBuilder('a')
            ->where('a.id_gamer = :gamerId')
            ->andWhere('a.etatachat = :etat')
            ->setParameter('gamerId', $gamerId)
            ->setParameter('etat', 1)
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return HistoriqueAchat[] Returns an array of HistoriqueAchat objects
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

//    public function findOneBySomeField($value): ?HistoriqueAchat
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
