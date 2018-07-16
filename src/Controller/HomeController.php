<?php
//on déclare le namespace
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

//declaration de la classe
//pour pouvoir utiliser Twig, on doit faire hériter notre classe de la classe Controller
class HomeController extends Controller
{

	/**
	*@Route("/", name="home")
	*je nomme ma route home
	*/
	public function home(){

		$pseudo = 'Toto';
		//on peut alors utiliser la méthode render() et travailler avec des fichiers html twig
		//il va chercher le fichier dans /templates
		//on peut passer des variables en paramètre :dans un tableau, en clé le nom de la variable disponible dans le fichier twig : ici dans twig, j'aurai accès à la vairable nom
		return $this->render('index.html.twig',
								array('nom' => $pseudo)
							);

	}

	/**
	*@Route("/bonjour/", name="bonjour")
	*/

	public function bonjour(){

		//ici on écrira le code 

		//tout controller doit renvoyer une réponse
		return new Response('<html><body><strong>Bonjour ! </strong></body></html>');

	}

	//Créer une page  pour l'url /exercice1/comment-allez-vous, qui affiche "bien, merci"

	/**
	*@Route("/exercice1/comment-allez-vous", name="cava")
	*/

	public function caVa(){

		return new Response('<html><body><strong>bien </strong> merci!</body></html>');

	}


	/*Créer une page  pour l'url /exercice2/heure
	Dans le controleur, stocker la date et l'heure dans une variable
	Passer cette variable à une vue twig (par ex exercice.html.twig), pour qu'elle affiche la date en gras*/

	/**
	*@Route("/exercice2/heure", name="heure")
	*/

	public function heure(){

		//stocke le datetime dans une variable
		$maDate = date('Y-m-d H:i:s');

		//je passe en paramètre à ma vue la variable que je viens de créer
		return $this->render('exercice.html.twig',
								array('date' => $maDate)
		);

	}


	/**
	* cette route va matcher bonjour/nimportequelleslettres)
	*@Route("bonjour/{nom}", name="bonjour2", requirements={"nom"="[a-zA-Z]+"})
	*
	*/

	public function bonjour2($nom)
	{
		//$nom est automatiquement envoyé en paramètre à notre méthode et contiendra tout ce qui suit bonjour/
		return $this->render('bonjour.html.twig', array('nom'=>$nom));
	}


	/*Créer une page  pour les url de type /exercice3/25/toto

	Ou 25 est un placeholder qui représente un age (donc uniquement des chiffres) et toto un pseudo (donc uniquement des lettres)
	Créer une vue (exercice3.html.twig) qui va afficher, Bonjour 'pseudo' tu as 'age' ans
	Mettre an au singulier si age = 1*/

	/**
	*@Route("exercice3/{age}/{pseudo}", name="exo3", requirements={"age"="\d+", "pseudo"="[a-zA-Z]+"})
	*
	*/

	//je récupère les placeholders dans mes variables (l'ordre importe peu, il faut rétuiliser les noms des placeholders)
	public function bonjourPseudoAge($pseudo, $age)
	{
		return $this->render('exercice3.html.twig',
								array('age'=>$age,
										'pseudo'=>$pseudo
								)
		);
	}

	/**
	*@Route("/testRedirect/")
	*/

	public function testRedirect(){

		//pour effectuer une redirection
		//on appelle la méthode redirectToRoute en lui passant en paramètre le nom de la route de destination
		return $this->redirectToRoute('home');

	}

	/*Créer un menu, sur toutes les pages suivantes qui permette de naviguer entre elles
	pages accessibles : 
	 /
	 /bonjour/
	 /bonjour/toto
	 /exercice1/comment-allez-vous
	 /exercice2/heure
	 /exercice3/33/toto

	liste des routes
	php bin/console debug:router


	inclure bootstrap et jquery (avec les fichiers sources et pas les cdn)

	faire hériter toutes nos vues twig du layout


	Créer une page pour la route exercice/genre/prix/produit
	où genre et produit sont des placeholders qui doivent être uniquement constitués de lettres
	et prix est uniquement constitué de chiffres


	Si genre est différent de homme ou femme, renvoyer un message d'erreur de votre choix
	Sinon, afficher un page avec le texte : 
	vous avez demandé : {produit}, prix {prix} € pour {genre}
	et afficher une photo qui doit être une photo de femme si genre est femme et homme sinon

	 */

	/**
	*@Route("exercice/{genre}/{prix}/{produit}", name="exoRecap", requirements={"genre"="[a-zA-Z]+", "prix"="[0-9]+", "produit"="[a-zA-Z]+"})
	*/

	public function exoRecap($genre, $prix, $produit){

		$message = '';

		if(!in_array($genre, ['homme', 'femme'])){
			$message = "genre invalide";
		}

		return $this->render('exoRecap.html.twig',
								array('genre'=>$genre,
										'prix'=>$prix,
										'produit'=>$produit,
										'message'=> $message
								)
		);

	}

	/**
	*@Route("/user-info/", name="user-info")
	*/
	public function showUser(){

		//pour vérifier si l'utilisateur est authentifié (quelque soit son rôle)
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

		//pour récupérer l'objet $user (l'utilisateur connecté)
		$moi = $this->getUser();
		dump($moi);

		return $this->render('user.info.html.twig', array('moi' => $moi));

	}

}