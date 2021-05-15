<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use App\Form\CommentType;
use App\Form\ArcticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo): Response
    {
        $articles = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles ,
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home(): Response
    {
        return $this->render('blog/home.html.twig');
    }

    /**
     * @Route("/blog/new", name="create")
     */
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        $article = new Article();
        
        $form = $this->createForm(ArcticleType::class , $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $article->setCreatedAt(new \DateTime())
                    ->setCategory($article->getCategory()->getTitel());

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute("blog_show", ['id' => $article->getId() ]);}

        return $this->render('blog/create.html.twig',[
            'articleForm' => $form->createView()]
        );
    }

     /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Article $article, Request $request, EntityManagerInterface $manager): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class , $comment);

        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $comment->setCreatedAt(new \DateTime())
                    ->setArticle($article);
            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute("blog_show", ['id' => $article->getId() ]);}

        return $this->render('blog/show.html.twig', [
            'article' => $article,
            'commentForm' => $form->createView()
        ]
            );
    }

 
}
