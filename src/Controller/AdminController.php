<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Article;
use App\Form\ArticleAdminType;

class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Article::class);
        $articles = $repository->findAll();

        return $this->render('admin/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
    *@Route("admin/article/add", name="admin-article-add")
    */

    public function addArticle(Request $request){

        $article = new Article();

        $form = $this->createForm(ArticleAdminType::class, $article);

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

            return $this->redirectToRoute('admin');

        }

        return $this->render('admin/article.add.html.twig',
                                array('form' => $form->createview())
            );

    }

    /**
    *@Route("admin/article/update/{id}", name="admin-article-update", requirements={"id"="\d+"})
    */

    public function updateArticle(Article $article, Request $request){


        $form = $this->createForm(ArticleAdminType::class, $article);

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

            $this->addFlash('success', 'Article ajouté !');

            return $this->redirectToRoute('admin');

        }

        return $this->render('admin/article.add.html.twig',
                                array('form' => $form->createview())
            );

    }

    /**
    *@Route("reserve/auteurs")
    */
    public function testReserve(){
    	//je peux vérifier dans mon controleur, si l'utilisateur connecté a le bon rôle
    	$this->denyAccessUnlessgranted('ROLE_AUTEUR', null, 'Pas possible !');

    	return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
    *@Route("reserve/moderateurs")
    *@Security("has_role('ROLE_MODERATEUR')")
    * revient au même qu'au dessus mais en utilisant les annotations 
    */
    public function testReserve2(){

    	return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);

    }
}
