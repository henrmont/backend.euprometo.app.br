<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\PostAttach;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function save(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Post[] Returns an array of Post objects
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

//    public function findOneBySomeField($value): ?Post
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    /**
     * @return Post[] Returns an array of post objects
     */
    public function getPosts()
    {
        $qb = $this->createQueryBuilder('post');

        $qb
            ->select('
                post.id AS id,
                post.title AS title,
                post.subtitle AS subtitle,
                post.content AS content,
                post.active AS active,
                post.created_at AS createdAt,
                post.updated_at AS updatedAt
            ')
            // ->innerJoin(PostAttach::class,'attach','WITH','post.id = attach.post_id')
            // ->where('post.deleted = false')
        ;

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * @return Post[] Returns an array of post objects
     */
    public function getPost($post)
    {
        $qb = $this->createQueryBuilder('post');

        $qb
            ->select('
                post.id AS id,
                post.title AS title,
                post.subtitle AS subtitle,
                post.content AS content,
                post.active AS active,
                post.created_at AS createdAt,
                post.updated_at AS updatedAt
            ')
            ->where('post.id = :post')
            ->setParameter('post',$post)
        ;

        return $qb->getQuery()->getArrayResult();
    }
}
