<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Category;
use App\Entity\Article;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class TestData extends Fixture
{

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder){
        //lors de l'instanciation de la classe, on va stocker l'encoder dans la propriété
        $this->encoder = $encoder;

    }

    public function load(ObjectManager $manager)
    {
        //pour créer 10 catégories

    	$cat = ['science', 'politique', 'littérature', 'sport', 'écologie', 'petites personnes', 'jardinage', 'pétanque', 'running', 'voyage'];

        for($i=0;$i<=9;$i++){

        	$categorie = new Category();

        	//j'utilise le compteur pour chercher la valeur dans le tableau cat
        	$categorie->setLibelle($cat[$i]);
        	$manager->persist($categorie);

        }

        //je crée un tableau d'auteurs
        $auteurs = ['Verlaine', "Hugo", 'Voltaire', 'Dumas', 'Duras', 'Garcia Marquez', 'Nabila'];

        //on crée 30 articles
        for($i=1;$i<=30;$i++){

        	$article = new Article();
        	$article->setTitle('Titre ' . $i);
        	$article->setContent('Contenu ' . $i . ' vraiment très intéressant');

        	//on va générer des dates aléatoirement

        	//Generate a timestamp using mt_rand.
            $timestamp = mt_rand(1, time());

            //Format that timestamp into a readable date string.
            $randomDate = date("Y-m-d H:i:s", $timestamp);

            //on l'envoie dans l'article
            $article->setDatePubli(new \DateTime($randomDate));

            //on chosit l'auteur aléatoirement dans le tableau défini avant la boucle
            $article->setAuthor($auteurs[array_rand($auteurs)]);

            $manager->persist($article);

        }


        //ajout de 10 utilisateurs
        for($i=1;$i<=10;$i++){

            $user = new User();

            $user->setUsername('Toto' . $i);
            $user->setEmail('toto' . $i . '@toto.to');
            //je donne le rôle admin à Toto1
            if($user->getUsername() == 'Toto1'){
                $user->setRoles(array('ROLE_USER', 'ROLE_ADMIN'));
            }
            else{

            $user->setRoles(array('ROLE_USER'));
            }
            $plainPassword = 'Toto' . $i;
            //j'utilise l'encoder pour être sur d'encode avec la bonne méthode (définie dans config/packages/security.yaml) le mot de passe
            $mdpEncoded = $this->encoder->encodePassword($user, $plainPassword);
            //j'envoie dans mon attribu password
            $user->setPassword($mdpEncoded);

            $manager->persist($user);


        }


        $manager->flush();
    }
}
