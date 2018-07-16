<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
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
