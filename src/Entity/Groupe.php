<?php

namespace App\Entity;

use App\Repository\GroupeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GroupeRepository::class)]
class Groupe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("groupe")]

    private ?int $id = null;
    #[Groups("groupe")]
    #[ORM\Column(length: 255,nullable: true)]
    private ?string $Nom_groupe = null;
    #[Groups("groupe")]

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $image = null;
    #[Groups("groupe")]

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $description = null;


    #[ORM\Column(nullable: true)]
    private ?int $nbr_user = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbr_max = null;

    #[ORM\OneToMany(mappedBy: 'idGroupe', targetEntity: Post::class)]
    private Collection $posts;


    #[ORM\OneToMany(mappedBy: 'idGroupe', targetEntity: MembreGroupe::class)]
    private Collection $membreGroupes;
    #[Groups("groupe")]

    #[ORM\Column(nullable: true)]
    private ?int $idOwner = null;
    #[Groups("groupe")]

    #[ORM\Column]
    private ?int $etat = null;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->membreGroupes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomGroupe(): ?string
    {
        return $this->Nom_groupe;
    }

    public function setNomGroupe(string $Nom_groupe): self
    {
        $this->Nom_groupe = $Nom_groupe;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getNbrUser(): ?int
    {
        return $this->nbr_user;
    }

    public function setNbrUser(int $nbr_user): self
    {
        $this->nbr_user = $nbr_user;

        return $this;
    }

    public function getNbrMax(): ?int
    {
        return $this->nbr_max;
    }

    public function setNbrMax(int $nbr_max): self
    {
        $this->nbr_max = $nbr_max;

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setIdGroupe($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getIdGroupe() === $this) {
                $post->setIdGroupe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MembreGroupe>
     */
    public function getMembreGroupes(): Collection
    {
        return $this->membreGroupes;
    }

    public function addMembreGroupe(MembreGroupe $membreGroupe): self
    {
        if (!$this->membreGroupes->contains($membreGroupe)) {
            $this->membreGroupes->add($membreGroupe);
            $membreGroupe->setIdGroupe($this);
        }

        return $this;
    }

    public function removeMembreGroupe(MembreGroupe $membreGroupe): self
    {
        if ($this->membreGroupes->removeElement($membreGroupe)) {
            // set the owning side to null (unless already changed)
            if ($membreGroupe->getIdGroupe() === $this) {
                $membreGroupe->setIdGroupe(null);
            }
        }

        return $this;
    }

    public function getIdOwner(): ?int
    {
        return $this->idOwner;
    }

    public function setIdOwner(int $idOwner): self
    {
        $this->idOwner = $idOwner;

        return $this;
    }

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(int $etat): self
    {
        $this->etat = $etat;

        return $this;
    }
}
