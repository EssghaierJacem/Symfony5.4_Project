<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    #[Route('/list/{var}', name: 'list_author')]
    public function listAuthor($var)
    {
        $authors = array(
            array('id' => 1, 'username' => 'Victor Hugo', 'email' => 'victor.hugo@gmail.com', 'nb_books' => 100),
            array('id' => 2, 'username' => 'William Shakespeare', 'email' => 'william.shakespeare@gmail.com', 'nb_books' => 200),
            array('id' => 3, 'username' => 'Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300),
        );

        return $this->render("author/list.html.twig", [
            'variable' => $var,
            'tabAuthors' => $authors,
        ]);
    }

    #[Route('/author/{id}', name: 'show_author')]
    public function showAuthor($id)
    {

        $authors = array(
            1 => array('id' => 1, 'username' => 'Victor Hugo', 'email' => 'victor.hugo@gmail.com', 'nb_books' => 100),
            2 => array('id' => 2, 'username' => 'William Shakespeare', 'email' => 'william.shakespeare@gmail.com', 'nb_books' => 200),
            3 => array('id' => 3, 'username' => 'Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300),
        );

        if (isset($authors[$id])) {
            $author = $authors[$id];
            $author['image'] = 'img/authors/' . $id . '.jpeg';
        } else {
            throw $this->createNotFoundException('Author not found');
        }

        return $this->render('author/showAuthor.html.twig', [
            'author' => $author,
        ]);
    }
    #[Route('/listAuthor', name: 'authors')]
    public function list(Request $request,AuthorRepository $repository)
    {
        $minBooks = $request->query->get('min_books');
        $maxBooks = $request->query->get('max_books');
        if ($minBooks !== null && $maxBooks !== null) {
            $authors = $repository->MinMaxBook($minBooks, $maxBooks);
        }else {
        $authors = $repository->findAll();
        }

        return $this->render(view:"author/listAuthors.html.twig",
            parameters: array(
             'tabAuthors'=>$authors,
             'minBooks' => $minBooks,
             'maxBooks' => $maxBooks,
        ));
    }
    #[Route('/add', name: 'add_authors')]
    public function addAuthor(ManagerRegistry $managerRegistry)
    {
        $author= new Author();
        $author->setEmail("author6@gmail.com");
        $author->setUsername("author6");
        $author->setNbrBook(40);
        // $em= $this->getDoctrine()->getManager();
        $em= $managerRegistry->getManager();
        $em->persist($author);
        $em->flush();
        return $this->redirectToRoute("authors");

    }


    #[Route('/update/{id}', name: 'update_authors')]
    public function updateAuthor($id,AuthorRepository $repository,ManagerRegistry $managerRegistry)
    {
        $author= $repository->find($id);
        $author->setEmail("author7@gmail.com");
        $author->setUsername("author7");
        // $em= $this->getDoctrine()->getManager();
        $em= $managerRegistry->getManager();
        $em->flush();
        return $this->redirectToRoute("authors");
    }


    #[Route('/delete/{id}', name: 'delete')]
    public function deleteAuthor($id,AuthorRepository $repository,ManagerRegistry $managerRegistry)
    {
        $author= $repository->find($id);
        $em= $managerRegistry->getManager();
        if($author->getNbrBook()==0){
            $em->remove($author);
            $em->flush();
        }
        else{
            return new  Response("Error!!");
        }
        return $this->redirectToRoute("authors");
    }

    #[Route('/listmail', name:'list_mail')]
    public function listByMail(AuthorRepository $authorRepository ){
        $authors= $authorRepository->listAuthorByEmail();
        return $this->render("author/email.html.twig", [
            'authors' => $authors,
        ]);
    }
    #[Route('/deletezero', name: 'deleteZero')]
    public function DeleteAuthorsZero(AuthorRepository $authorRepository,ManagerRegistry $managerRegistry): Response
    {
        $deletedAuthorsCount = $authorRepository->ZeroBooks();
        $em= $managerRegistry->getManager();

        $em->flush();
        return $this->redirectToRoute("authors");

    }
}