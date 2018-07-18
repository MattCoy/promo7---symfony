<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\User;

class UserController extends Controller
{
    /**
     * @Route("/users", name="user-list")
     */
    public function showAll()
    {

    	$users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->render('user/users.html.twig', [
            'users' => $users
        ]);
    }

    /**
    *@Route("/user/{id}", name="show-user", requirements={"id"="\d+"})
    */
    public function show(User $user){

    	return $this->render('user/user.html.twig', [
            'user' => $user
        ]);

    }
}
