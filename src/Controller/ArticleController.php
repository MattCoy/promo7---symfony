<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Article as Article;
use App\Form\ArticleUserType;
use Symfony\Component\HttpFoundation\File\File;
use App\Service\FileUploader;

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
    public function addArticle(Request $request, FileUploader $uploader)
    {

        $this->denyAccessunlessgranted('IS_AUTHENTICATED_FULLY');

        $article = new Article();

    	$form = $this->createForm(ArticleUserType::class, $article);

        //je vais demander à mon objet form de gérer les données envoyées par l'utilisateur
         $form->handleRequest($request);

         //je vais faire le traitement d'ajout si le formulaire a été envoyé et s'il est valide
         if($form->isSubmitted() && $form->isvalid()){

            //$form->getData() contient les données envoyées
            //ici, on charge le formulaire de remplir notre objet catégorie avec les données

            $article = $form->getData();

            //l'utilisateur connecté est l'auteur de l'article
            $article->setUser($this->getUser());

            //on fixe la date de publication
            $article->setDatePubli(new \DateTime(date('Y-m-d H:i:s')));

            //ceci va contenir l'image envoyée
            $file = $article->getImage();

            //j'appelle mon service FileUploader
            $fileName = $uploader->upload($file);

            //on met à jour la propriété image, qui doit contenir le nom du fichier et pas le fichier lui même pour pouvoir persister l'article
            $article->setImage($fileName);

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

        $articles = $repository->myFindAll();

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
        $date_post = "2000-07-11 14:00:00";
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

    public function updateArticle(Article $article, Request $request, FileUploader $uploader){

        //j'utilise mon voter pour déterminer si l'utilisateur peut modifier cet article
        $this->denyAccessUnlessGranted('edit', $article);

        //je stocke le nom du fichier
        $fileName = $article->getImage();

        //si l'article a bien une image
        if($article->getImage()){

            //pour pouvoir générer le formulaire, on doit transformer le nom du fichier stocké pour l'instant dans l'attribut image en instance de la classe File (ce qui est attendu par le formulaire)
            $article->setImage(new File($this->getParameter('articles_image_directory') . '/' . $article->getImage()));
        }

        $form = $this->createForm(ArticleUserType::class, $article);

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

                //on utilise notre service FileUploader
                $fileName = $uploader->upload($file, $fileName);
            }

            //on met à jour la propriété image qui doit contenir le nom du fichier pour être persistée
            //fileName contient soit le nouveau nom de fichier si on a reçu une nouvelle image, soit l'ancien si l'utilisateur n'a pas modifié l'image
            $article->setImage($fileName);

            

            //je n'ai plus qu'à persister mon article et faire un flush
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

        //j'utilise mon voter pour déterminer si l'utilisateur peut supprimer cet article
        $this->denyAccessUnlessGranted('delete', $article);

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
