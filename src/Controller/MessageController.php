<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MessageController extends Controller
{
    /**
     * @Route("/message/add", name="message-add")
     */
    public function addMessage(Request $request)
    {

        $message = new Message();

        $form = $this->createForm(MessageType::class, $message);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() ){

            $message = $form->getData();
             //c'est moi qui fixe la date d'envoi
            //je dois lui envoyer un objet de classe DateTime
            $message->setDateenvoi(new \DateTime(date('Y-m-d H:i:s')));

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($message);

            $entityManager->flush();

            //c'est bon, je crée un message de réussite et je renvoie               vers la liste des messages

            $this->addFlash('success', 'Message ajouté !');

        }

        return $this->render('message/add.html.twig',
                                ['form' => $form->createView()]
            );
    }

    /**
     * @Route("/messages/", name="all-messages")
     */

    public function showAll(){

        $repository = $this->getDoctrine()->getRepository(Message::class);

        $messages = $repository->findAll();

        return $this->render('message/all.html.twig',
            ['messages' => $messages]
        );

    }

    /**
     * @Route("/message/{id}", name="message", requirements={"id"="\d+"})
     */

    public function show(Message $message){


        return $this->render('message/message.html.twig',
            ['message' => $message]
        );

    }
}
