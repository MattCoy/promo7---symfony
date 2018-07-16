<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Article as Article;
use App\Form\ArticleType;

class ArticleController extends Controller
{

    /*Créer un formulaire d'ajout/modification d'article
    modifier les controleurs d'ajout /modification d'article pour utliser ce formulaire
    le titre ne doit pas être vide et ne doit pas faire plus de 50 caractères
    le contenu doit faire au moins 10 caractères
    l'auteur ne doit pas être vide
    */


    /**
     * @Route("/article", name="article")
     */
    public function index()
    {
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
        ]);
    }

    /**
    *@Route("/article/add", name="add-article")
    */
    public function addArticle(Request $request)
    {

        $article = new Article();

    	$form = $this->createForm(ArticleType::class, $article);

        //je vais demander à mon objet form de gérer les données envoyées par l'utilisateur
         $form->handleRequest($request);

         //je vais faire le traitement d'ajout si le formulaire a été envoyé et s'il est valide
         if($form->isSubmitted() && $form->isvalid()){

            //$form->getData() contient les données envoyées
            //ici, on charge le formulaire de remplir notre objet catégorie avec les données

            $article = $form->getData();

            //je n'ai plus qu'à persister ma catégorie et faire un flush
            $entityManager = $this->getDoctrine()->getManager(); 

            $entityManager->persist($article);

            $entityManager->flush();

            //c'est bon, je crée un message de réussite et je renvoie vers la liste des catégories

            $this->addFlash('success', 'Article ajouté !');

            return $this->redirectToRoute('all-articles');

         }



    	return $this->render('article/add.html.twig',
                                array('form' => $form->createview())
            );

    }

    /**
    *@Route("/articles", name="all-articles")
    */

    public function showAll(){

        $repository = $this->getDoctrine()->getRepository(Article::class);

        $articles = $repository->findAll();

        return $this->render('article/articles.html.twig',
                                array('articles'=>$articles)
        );


    }

    /*
    Dans la page liste des articles, ajouter un lien vers la page d'un article (par exemple article/1 )
    Créer une page qui va afficher les détails d'un article.
    On utilise l'id de l'article pour récupérer l'article (placeholder dans l'url)*/

    /**
    *@Route("article/{id}", name="show", requirements={"id"="\d+"})
    */

    public function show($id)
    {

        $repository = $this->getDoctrine()->getRepository(Article::class);
        $article = $repository->find($id);

        //nous permet de renvoyer un message d'erreur si aucun id ne correspond
        if (!$article) {
            throw $this->createNotFoundException(
                'No article found for id '.$id
            );
        }

        return $this->render('article/article.html.twig',
                                array('article' => $article)
        );

    }

    /**
    *@Route("articles/after/", name="after")
    */

    public function showPostedAfter(){
        //pour l'instant on met la date en dur
        $date_post = "2018-07-11 14:00:00";
        //on appelle le repository de notre entité Article
        $repository = $this->getDoctrine()->getRepository(Article::class);
        //on execute la méthode custom et on récupère les articles trouvés
        $articles = $repository->findAllPostedAfter($date_post);

        //j'utilise la fonction dump() améliorée
        dump($articles);

        $articles2 = $repository->findAllPostedAfter2($date_post);
        
        dump($articles2);

        return $this->render('article/articles.recent.html.twig',
                                array('articles' => $articles,
                                        'articles2' => $articles2
                                )
        );

    }

    //créer les pages qui vont permettre de modifier ou supprimer un article

    /**
    *@Route("article/update/{id}", name="article-update", requirements={"id"="\d+"})
    */

    public function updateArticle(Article $article, request $request){


        $form = $this->createForm(ArticleType::class, $article);

        //je vais demander à mon objet form de gérer les données envoyées par l'utilisateur
         $form->handleRequest($request);

         //je vais faire le traitement d'ajout si le formulaire a été envoyé et s'il est valide
         if($form->isSubmitted() && $form->isvalid()){

            //$form->getData() contient les données envoyées
            //ici, on charge le formulaire de remplir notre objet catégorie avec les données

            $article = $form->getData();

            //je n'ai plus qu'à persister ma catégorie et faire un flush
            $entityManager = $this->getDoctrine()->getManager(); 

            $entityManager->flush();

            //c'est bon, je crée un message de réussite et je renvoie vers la liste des catégories

            $this->addFlash('success', 'Article modifié !');

            return $this->redirectToRoute('all-articles');

         }



        return $this->render('article/update.html.twig',
                                array('form' => $form->createview())
            );


    }

    /**
    *@Route("article/delete/{id}", name="article-delete", requirements={"id"="\d+"})
    */

    public function deleteArticle(Article $article){

        //récupération de l'entity manager
         $entityManager = $this->getDoctrine()->getManager();

         //je veux supprimer cette catégorie
         $entityManager->remove($article);

         //j'exécute la requête
         $entityManager->flush();

         //créer un message flash et renvoyer sur la liste des dernières catégories

         $this->addFlash('warning', 'Article suprimé !');

         return $this->redirectToRoute('all-articles');

    }

}
