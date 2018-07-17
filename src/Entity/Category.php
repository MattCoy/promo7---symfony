<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\Length(min=3, 
     *                  max=30, 
     *                  minMessage="La catégorie doit faire plus de 2 caractères", 
    *                   maxMessage="La catégorie ne doit pas faire plus de 30 caractères")
     */
    private $libelle;

    /**
    *@ORM\OneToMany(targetEntity="App\Entity\Article", mappedBy="category")
    */
    private $articles;

    public function __construct(){

        $this->articles = new ArrayCollection();

    }

    public function getId()
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getArticles(): ?Collection
    {
        return $this->articles;
    }
}
