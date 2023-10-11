<?php

namespace App\Controller;

use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function list(AuthorRepository $repository)
    {
        $authors = $repository->findAll();
        return $this->render(view:"author/listAuthors.html.twig",
            parameters: array(
            'tabAuthors'=>$authors
        ));
    }
}