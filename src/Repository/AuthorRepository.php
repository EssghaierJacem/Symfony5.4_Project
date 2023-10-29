<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 *
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

//    /**
//     * @return Author[] Returns an array of Author objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Author
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

     public function listAuthorByEmail(): array{
         $qb= $this->createQueryBuilder('a')
             ->orderBy('a.email','ASC');
         return $qb->getQuery()->getResult();
     }

    public function MinMaxBook($minBooks, $maxBooks)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery("
        SELECT a
        FROM App\Entity\Author a
        LEFT JOIN a.books b
        HAVING COUNT(b) BETWEEN :min_books AND :max_books OR COUNT(b) = 0
    ");

        $query->setParameter('min_books', $minBooks);
        $query->setParameter('max_books', $maxBooks);

        return $query->getResult();
    }
    public function ZeroBooks() {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery("
        DELETE FROM App\Entity\Author a
        WHERE (
            SELECT COUNT(b.ref)
            FROM App\Entity\Book b
            WHERE b.author = a
        ) = 0
    ");

        return $query->execute();
    }

}
