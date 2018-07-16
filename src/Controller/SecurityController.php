<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {

    	//récupération d'éventuelles erreurs de login
    	$error = $authenticationUtils->getLastAuthenticationError();

    	//récupération du nom d'utilisateur (pour pré remplir le champ login en cas d'erreur)
    	$lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
    *@Route("/register/", name="register")
    */

    public function register(Request $request, UserPasswordEncoderInterface $encoder){

        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            //on récupère les données envoyées via le formulaire
            $user =$form->getData();
            //à ce moment, $user->getPassword() vaut null , seul $plainPassword contient le mdp en clair
            //je dois encoder le mdp en clair (plainpassword) et le mettre dans password

            $mdpEncoded = $encoder->encodePassword($user, $user->getPlainPassword());

            $user->setPassword($mdpEncoded);

            //ne pas oublier d'effeçer le mdp en claire
            $user->eraseCredentials();
            dump($user);

            //je définis un rôle par défaut
            $user->setRoles(array('ROLE_USER'));

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($user);

            $entityManager->flush();

             $this->addFlash(
                'success',
                'Vous êtes bien inscrit, vous pouvez vous connecter !'
            );

            //on renvoie sur le login
            return $this->redirectToRoute('login');


        }


        //on place la vue dans le sous-dossier templates/security/
        return $this->render('security/register.html.twig', array(
            'form' => $form->createView()
        ));


    }
}
