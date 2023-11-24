<?php

namespace App\Repository;

use App\Entity\CommentaireNews;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CommentaireNews>
 *
 * @method CommentaireNews|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommentaireNews|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommentaireNews[]    findAll()
 * @method CommentaireNews[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentaireNewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommentaireNews::class);
    }

    public function save(CommentaireNews $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CommentaireNews $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findWithUserByNews($newsId)
    {
        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.idNews = :newsId')
            ->setParameter('newsId', $newsId)
            ->leftJoin('c.user', 'u')
            ->addSelect('u')
            ->orderBy('c.date', 'DESC');

        return $qb->getQuery()->getResult();
    }


//    /**
//     * @return CommentaireNews[] Returns an array of CommentaireNews objects
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

//    public function findOneBySomeField($value): ?CommentaireNews
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
