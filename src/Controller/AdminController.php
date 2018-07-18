<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Article;
use App\Form\ArticleAdminType;
use Symfony\Component\HttpFoundation\File\File;
use App\Service\FileUploader;

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

    public function addArticle(Request $request, FileUploader $uploader){

        $article = new Article();

        $form = $this->createForm(ArticleAdminType::class, $article);

        //je vais demander à mon objet form de gérer les données envoyées par l'utilisateur
         $form->handleRequest($request);

         //je vais faire le traitement d'ajout si le formulaire a été envoyé et s'il est valide
         if($form->isSubmitted() && $form->isvalid()){

            //$form->getData() contient les données envoyées
            //ici, on charge le formulaire de remplir notre objet catégorie avec les données

            $article = $form->getData();

            //ceci va contenir l'image envoyée
            $file = $article->getImage();

            //on génère un nouveau nom
            $fileName = $uploader->upload($file);

            //on met à jour la propriété image, qui doit contenir le nom du fichier et pas le fichier lui même pour pouvoir persister l'article
            $article->setImage($fileName);

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

    public function updateArticle(Article $article, Request $request, FileUploader $uploader){

        //je stocke le nom du fichier
        $fileName = $article->getImage();

        //si l'article a bien une image
        if($article->getImage()){

            //pour pouvoir générer le formulaire, on doit transformer le nom du fichier stocké pour l'instant dans l'attribut image en instance de la classe File (ce qui est attendu par le formulaire)
            $article->setImage(new File($this->getParameter('articles_image_directory') . '/' . $article->getImage()));
        }


        $form = $this->createForm(ArticleAdminType::class, $article);

        //je vais demander à mon objet form de gérer les données envoyées par l'utilisateur
         $form->handleRequest($request);

         //je vais faire le traitement d'ajout si le formulaire a été envoyé et s'il est valide
         if($form->isSubmitted() && $form->isvalid()){

            //$form->getData() contient les données envoyées
            //ici, on charge le formulaire de remplir notre objet catégorie avec les données

            $article = $form->getData();

            //je ne fais le traitement d'upload que si on m'a envoyé un fichier
            if($article->getImage()){
                //on récupère un objet de classe File
                $file = $article->getImage();

                //on génère un nouveau nom
                $fileName = $uploader->upload($file, $fileName);
            }

            //on met à jour la propriété image qui doit contenir le nom du fichier pour être persistée
            //fileName contient soit le nouveau nom de fichier si on a reçu une nouvelle image, soit l'ancien si l'utilisateur n'a pas modifié l'image
            $article->setImage($fileName);

            //je n'ai plus qu'à persister mon article et faire un flush
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
