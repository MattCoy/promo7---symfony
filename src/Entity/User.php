<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=70)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(name="roles", type="array")
     */
    private $roles;

    /**
    *on indique à doctrine la relation OneToMany
    *@ORM\OneToMany(targetEntity="App\Entity\Article", mappedBy="user")
    * ceci ne va pas rajouter de champ dans la table
    */
    private $articles;

    /**
     * @param mixed
     * je ne veux pas persister le mdp en clair,
     * donc je ne le déclare pas en column doctrine
     */
    private $plainPassword;

    /**
    *@ORM\Column(type="string", nullable=true)
    *@Assert\Image(maxSize="1000k")
    */
    private $image;

    //on écrit le constructeur pour mettre isActive  true lors de l'iinstanciation
    public function __construct(){
        $this->isActive = true; //par défaut, un user est actif

        $this->articles = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /*méthodes de UserInterface à implémenter */

    public function eraseCredentials(){
        //par mesure de sécurité on va effeçer le mdp en clair
        $this->plainPassword = null;
    }

    public function getSalt(){

        //on va utiliser l'encoder bcrypt de Symfony
        //qui va lui même gérer le salt
        //on est quand mêm obligé d'écrire cette méthode car on implément l'interface UserInterface
        return null;
    }

    public function serialize(){
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    public function unserialize($serialized){
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized, ['allowed_classes' => false]);
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @return Collection|Article[]
     */
    public function getArticles(): Collection{
        return $this->articles;
    }

    public function getImage(){

        return $this->image;

    }

    public function setImage($image){

        $this->image = $image;
        return $this;

    }
}
