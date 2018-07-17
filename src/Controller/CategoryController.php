<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Category;
use App\Form\CategoryType;

class CategoryController extends Controller
{

    /**
     * @Route("/category/lastfive", name="last-five-category")
     */
    public function lastFive()
    {

    	$repository = $this->getDoctrine()->getRepository(Category::class);

    	$categories = $repository->findLastFive();

        return $this->render('category/last.five.html.twig',
        						array('categories' => $categories)
    	);
    }

    /**
     *@Route("/categories", name="all-categories")
     */

    public function showAll(){

        $repository = $this->getDoctrine()->getRepository(Category::class);

        $categories = $repository->findAll();

        return $this->render('category/categories.html.twig',
            array('categories'=>$categories)
        );


    }


    /**
     * @Route("/category/add", name="add-category")
     */
    public function add(Request $request)
    {

    	//je crée un objet de classe Category, pour l'instant vide
    	//je vais m'en servir pour à la fois générer le formulaire et pour récupérer les données envoyées
    	$category = new Category();

    	//je passe en paramètre l'objet Category à mon formulaire
    	//je construis mon formulaire grâce à ma classe CategoryType
    	$form = $this->createForm(CategoryType::class, $category);

    	//je vais demander à mon objet form de gérer les données envoyées par l'utilisateur
    	 $form->handleRequest($request);

    	 //je vais faire le traitement d'ajout si le formulaire a été envoyé et s'il est valide
    	 if($form->isSubmitted() && $form->isvalid()){

    	 	//$form->getData() contient les données envoyées
    	 	//ici, on charge le formulaire de remplir notre objet catégorie avec les données

    	 	$category = $form->getData();

    	 	//je n'ai plus qu'à persister ma catégorie et faire un flush
    	 	$entityManager = $this->getDoctrine()->getManager(); 

    		$entityManager->persist($category);

    		$entityManager->flush();

    		//c'est bon, je crée un message de réussite et je renvoie vers la liste des catégories

    		$this->addFlash('success', 'Catégorie ajoutée !');

    	 	return $this->redirectToRoute('last-five-category');

    	 }


    	//je passe en paramètre le formulaire que je viens de créer
        return $this->render('category/add.html.twig',
        						array('form' => $form->createView())
    	);
    }

    /**
    *@Route("category/update/{id}", name="category-update", requirements={"id"="\d+"})
    */

    public function updateCategory(Category $category, Request $request){

    	/*
    	on utilise une fonctionnalité de symfony: le param converter
    	Au lieu de récupérer l'id en argument avec $id, on indique à Symfony que l'on veut récupérer un objet de classe Category : symfony se charge de récupérer l'objet avec $repository->find($id) tout seul
    	On récupère donc $category qui est un objet de classe category

    	Cela nous évite d'écrire :
    	$repository = $this->getDoctrine()->getRepository(Category::class);

    	$category = $repository->find($id);
    	*/

    	//je crée le formulaire
    	$form = $this->createForm(CategoryType::class, $category);

    	//je vais demander à mon objet form de gérer les données envoyées par l'utilisateur
    	 $form->handleRequest($request);

    	 if($form->isSubmitted() && $form->isvalid()){

    	 	$category = $form->getData();

	    	//Pour modifier la catégorie : en dur
	    	// $category->setLibelle('économie');

	    	//récupération de l'entity manager
    	 	$entityManager = $this->getDoctrine()->getManager();

    	 	$entityManager->flush();

    	 	$this->addFlash('success', 'Catégorie modifiée !');

    	 	return $this->redirectToRoute('all-categories');

    	 }
	
		return $this->render('category/update.html.twig',
								array('form' => $form->createView())
		);    	 

    }

    /**
    *@Route("category/delete/{id}", name="category-delete", requirements={"id"="\d+"})
    */

    public function deleteCategory(Category $category){

    	//récupération de l'entity manager
    	 $entityManager = $this->getDoctrine()->getManager();

    	 //je veux supprimer cette catégorie
    	 $entityManager->remove($category);

    	 //j'exécute la requête
    	 $entityManager->flush();

    	 //créer un message flash et renvoyer sur la liste des dernières catégories

    	 $this->addFlash('warning', 'Catégorie suprimée !');

    	 return $this->redirectToRoute('last-five-category');

    }

    /**
    *@Route("/category/{id}", name="show-category", requirements={"id"="\d+"})
    */
    public function  show(Category $category){

       return $this->render('category/category.html.twig',
                            array('category' => $category)
        );

    }


}
