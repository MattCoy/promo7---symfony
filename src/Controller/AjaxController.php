<?php

namespace App\Controller;

use http\Env\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\User;
use App\Entity\Article;
use Symfony\Component\HttpFoundation\Request;

class AjaxController extends Controller
{
    /**
     * @Route("/ajax/search-by-author/{id}", name="ajax-search-by-author", requirements={"id"="\d+"})
     */
    public function searchByAuthor(User $user)
    {
    	//j'ai récupéré l'utilisateur sélectionné grâce à mon paramètre {id} dans la route et au paramConverter

    	$repository =$this->getDoctrine()->getRepository(Article::class);
    	//on rajoute à la suite de findBy le nom de la propriété par laquelle on fait la recherche
    	//doctrine va comprendre et faire la requête appropriée
    	$articles = $repository->findByUser($user);

        return $this->render('ajax/articles.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
    *@Route("ajax/search-2/", name="ajax-search-by-author2")
    */
    public function searchByAuthor2(Request $request){

    	//dump($request->request->all());
    	$idUser = $request->request->get('idUser', 'invalid');

    	//si l'id reçue n'est pas un nombre, on renvoie une réponse json avec status ko
    	if(!preg_match('#\d+#', $idUser)){
    		return $this->json(array('status'=>'ko'));
    	}

    	//je récupère l'utilisateur
    	$user = $this->getDoctrine()->getRepository(User::class)->find($idUser);

    	//on récupère les articles associés
    	$repository =$this->getDoctrine()->getRepository(Article::class);
    	$articles = $repository->findByUser($user);

    	//pour pouvoir renvoyer mes articles, je génère un tableau de tableaux avec les informations
    	$result = [];
    	foreach($articles as $article){

    		$result[] = array(
    				'title' => $article->getTitle(),
    				'datepubli' => $article->getDatePubli()->format('d/m/Y'),
    				'content' => $article->getContent(),
    				'image' => $article->getImage(),
    				//je peux générer le lien vers la fiche d'un article grâce à generateUrl('nom de la route', array('nom du param'=> valeur))
    				'url' => $this->generateUrl('show', ['id' => $article->getId()])
    		);

    	}

    	return $this->json(array('status'=>'ok', 'articles' => $result));

    }

    /**
     * @Route("ajax/search/by/title", name="ajax-search-by-title")
     */
    public function searchByTitle(Request $request){

        $title = $request->query->get('title', null);//=> $_GET['title']

        if(!$title){

            return new Response('titre invalide');

        }

        $articles = $this->getDoctrine()->getRepository(Article::class)->searchTitleLike($title);

        dump($articles);

        return $this->render('ajax/articles.html.twig', ['articles' => $articles]);

    }


}

