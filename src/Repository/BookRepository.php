<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\DocBlock\Tags\Author;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }
    public function searchBookByRef($ref): array
    {
        $qb = $this->createQueryBuilder('b')
            ->where('b.ref = :ref')
            ->setParameter('ref', $ref);
        return $qb->getQuery()->getResult();
    }

    public function booksListByAuthors():array
    {
        $qb = $this->createQueryBuilder('b')
            ->leftJoin('b.author', 'a')
            ->orderBy('a.username', 'ASC');
           return $qb->getQuery()->getResult();
    }
    public function BookPublishedDate():array
    {
        $qb = $this->createQueryBuilder('b')
            ->where('b.publicationDate < :date')
            ->setParameter('date', new \DateTime('2023-01-01'))
            ->innerJoin('b.author', 'a')
            ->groupBy('a.id')
            ->having('COUNT(b) > 10');
        return $qb->getQuery()->getResult();
    }
    public function UpdateBook(): int
    {
        $qb = $this->createQueryBuilder('b')
            ->update(Book::class, 'b')
            ->set('b.category', ':newCategory')
            ->where('b.category = :oldCategory')
            ->setParameter('newCategory', 'Romance')
            ->setParameter('oldCategory', 'Science-Fiction');

        return $qb->getQuery()->execute();

    }

    public function countRomanceBooks()
    {
        $category = "Romance";

        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('
            SELECT COUNT(b.ref)
            FROM App\Entity\Book b
            WHERE b.category = :category
        ');
        $query->setParameter('category', $category);

        return $query->getResult();
    }
    public function BookSortDate()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery("
            SELECT b
            FROM App\Entity\Book b
            WHERE b.publicationDate BETWEEN '2014-01-01' AND '2018-12-31'
        ");

        return $query->getResult();
    }


//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    /* public function findByAuthor($author): array
     {
         $qb= $this->createQueryBuilder('b')
             ->select('b')
             ->innerJoin('b.author','a')
             ->andWhere('a.username =:  username')
             ->setParameter(key: 'username', value: $author ) ->orderBy('b.ref', 'DESC')
             ->setMaxResults(10);
         return $qb->getQuery()->getResult();
     }
    */
    /* public function findByDate($from, $to): array
     {
         $qb = $this->createQueryBuilder('b')
             ->select('b')
             ->andWhere('b.PublicationDate BETWEEN :from AND :to')
             ->setParameter('from', new \DateTime('2023-01-01 00:00:00'))
             ->setParameter('to', new \DateTime('2023-12-31 23:59:59'))
             ->orderBy('b.PublicationDate', 'DESC')
             ->setMaxResults(10);

         return $qb->getQuery()->getResult();
     }
     */
    /* public function XYZ(){
        $em = $this->getEntityManager();
        $query =$em -> createQuery('');
        return $query->getResult();
    }
    */
}
