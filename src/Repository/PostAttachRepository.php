<?php

namespace App\Repository;

use App\Entity\PostAttach;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PostAttach>
 *
 * @method PostAttach|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostAttach|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostAttach[]    findAll()
 * @method PostAttach[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostAttachRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostAttach::class);
    }

    public function save(PostAttach $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PostAttach $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PostAttach[] Returns an array of PostAttach objects
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

//    public function findOneBySomeField($value): ?PostAttach
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    /**
     * @return PostAttach[] Returns an array of postAttach objects
     */
    public function getPostAttachs($post)
    {
        $qb = $this->createQueryBuilder('attach');

        $qb
            ->select('
                attach.file as file
            ')
            ->where('attach.post_id = :post')
            ->setParameter('post',$post)
        ;

        return $qb->getQuery()->getArrayResult();
    }
}
