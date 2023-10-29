<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }
    #[Route('/addBook', name: 'add_book')]
    public function addBook(Request $request,ManagerRegistry $managerRegistry)
    {
        $book= new Book();
        $form= $this->createForm(BookType::class,$book);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            $nbrBook= $book->getAuthor()->getNbrBook();
            // var_dump($nbrBook).die();
            $book->getAuthor()->setNbrBook($nbrBook+1);
            $book->setEnabled(true);
            $em= $managerRegistry->getManager();
            $em->persist($book);
            $em->flush();
            return new Response("Done!");
        }
        return $this->renderForm('book/add.html.twig',array("formulaireBook"=>$form));
    }

    #[Route('/updateBook/{ref}', name: 'update_book')]
    public function updateBook($ref,BookRepository $repository,ManagerRegistry $manager, Request $request): Response
    {
        $book = $repository->find($ref);
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $manager->getManager();
            $em->flush();
            return $this->redirectToRoute('list_book');
        }
        return $this->renderForm('book/updateBook.html.twig', ['form' => $form]);
    }
    #[Route('/listBook', name: 'list_book')]
    public function listBook(Request $request, BookRepository $bookrepository)
    {
        $ref = $request->query->get('ref');
        $books = [];
        if ($ref) {
            $books = $bookrepository->searchBookByRef($ref);
        } else {
            $books = $bookrepository->findAll();
        }
        $romancebooks = $bookrepository->countRomanceBooks();
        return $this->render('book/show.html.twig', [
            'books' => $books,
            'romancebooks' => $romancebooks,
        ]);
    }
    #[Route('/DateBook', name: 'date_book')]
    public function listBookDate(Request $request, BookRepository $bookrepository){
        $books = $bookrepository->BookSortDate();
        return $this->render('book/datebook.html.twig', [
            'books' => $books,
        ]);
    }
    #[Route('/showBook/{ref}', name: 'show_book')]
    public function showAuthor($ref,BookRepository $repository)
    {
        return $this->render('book/showBook.html.twig',
            array('book'=>$repository->find($ref)));
    }
    #[Route('/deleteBook/{ref}', name:'delete_book')]
    public function deleteBook($ref,BookRepository $bookRepository, ManagerRegistry $managerRegistry)
    {
        $book = $bookRepository->find($ref);

        $em = $managerRegistry->getManager();
        $em->remove($book);
        $em->flush();

        return $this->redirectToRoute('list_book');
    }
    #[Route("/booksByAuthors", name:"books_by_authors")]
    public function booksByAuthors(BookRepository $bookRepository)
    {
        $books = $bookRepository->booksListByAuthors();

        return $this->render('book/bAuthors.html.twig', [
            'books' => $books,
        ]);
    }
    #[Route("/booksByDate", name:"books_by_date")]
    public function BooksDatePublished(BookRepository $bookRepository)
    {
        $books = $bookRepository->BookPublishedDate();

        return $this->render('book/BookPublishedDate.html.twig', [
            'books' => $books,
        ]);
    }
    #[Route("/UpdateBooks", name:"BooksUpdate")]
    public function UpdateBookGenre(BookRepository $bookRepository, ManagerRegistry $managerRegistry): Response
    {
        $bookRepository->UpdateBook();
        $em = $managerRegistry->getManager();
        $em->flush();
        return $this->redirectToRoute('list_book');
    }
    /*#[Route('/showbookAuthor/{date}', name:'author_book')]
            public function ShowBookAuthor($date, BookRepository $bookRepository, ManagerRegistry $managerRegistry){
            $from = new \DateTime($date . '2023-01-01 ');
            $books = $bookRepository->findByDate($from);
            return $this->render('book/showBookAuthor.html.twig', [
            'books' => $books,]);
    }
    */
    }



