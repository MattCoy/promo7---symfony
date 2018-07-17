<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\User;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 */
class Article
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max=50, maxMessage="Pas plus de 50 caractères svp")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=10, minMessage="au moins 10 caractères svp")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_publi;

    /**
    *On va expliquer à doctrine que cette propriété fait référence à l'entité user et qu'il s'agit dune relation ManyToOne
    *@ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="articles")
    */
    private $user;

    /**
    *
    *@ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="articles")
    */
    private $category;

    public function getId()
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getDatePubli(): ?\DateTimeInterface
    {
        return $this->date_publi;
    }

    public function setDatePubli(\DateTimeInterface $date_publi): self
    {
        $this->date_publi = $date_publi;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): self
    {
        $this->category = $category;

        return $this;
    }
}
