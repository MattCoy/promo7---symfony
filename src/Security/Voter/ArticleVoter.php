<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Article;
use App\Entity\User;

class ArticleVoter extends Voter
{

    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';

    protected function supports($attribute, $subject)
    {
        //si l'attribut n'est pas supproté, on renvoie false
        if(!in_array($attribute, array(self::VIEW, self::EDIT, self::DELETE))){
            return false;
        }

        //si $subject n'est pas un objet de classe Article
        if(!$subject instanceof Article){
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {

        //je récupère l'utilisateur connecté
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        //grâce à la méthode support(), on sait que $subject (passé en paramètre) est un objet de classe Article
        $article = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($article, $user);
                break;
            case self::VIEW:
                return $this->canView($article, $user);
                break;
            case self::DELETE:
                return $this->canDelete($article, $user);
                break;
        }

        return false;
    }

    //je crée un méthode qui va déterminer si l'utilisateur peut modifier l'article
    private function canEdit(Article $article, User $user){

        //l'utilisateur peut modifier l'article s'il en est l'auteur
        if($user == $article->getUser()){
            return true;
        }
        else{
            return false;
        }

    }

    //méthode qui détermine si l'utilisateur peut voir l'article
    private function canView(Article $article, User $user){

        //
        return true;

    }

    //méthode qui détermine si l'utilisateur peut supprimer l'article
    private function canDelete(Article $article, User $user){

        //l'utilisateur peut supprimer l'article s'il en est l'auteur
        if($user == $article->getUser()){
            return true;
        }
        else{
            return false;
        }

    }
}
