<?php

namespace App\Entity;

use App\Repository\NewsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: NewsRepository::class)]
class News
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("news")]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups("news")]
    private ?\DateTimeInterface $dateN = null;

    #[UniqueEntity("titre")]
    #[ORM\Column(length: 255)]
    #[Groups("news")]
    private ?string $titre = null;

    #[ORM\Column(length: 65535)]
    #[Assert\Length(min: 10)]
    #[Groups("news")]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'idNews', targetEntity: CommentaireNews::class, cascade: ["remove"])]
    private Collection $commentaireNews;

    #[ORM\ManyToOne(inversedBy: 'news')]
    private ?Jeux $idJeux = null;

    #[Assert\NotBlank]
    #[Assert\Image(mimeTypes: ['image/jpeg', 'image/png', 'image/gif'], mimeTypesMessage: 'Le fichier doit être une image de type JPEG, PNG ou GIF')]
    #[ORM\Column]
    #[Groups("news")]
    private ?string $image = null;

    public function __construct()
    {
        $this->commentaireNews = new ArrayCollection();
        $this->dateN = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateN(): ?\DateTimeInterface
    {
        return $this->dateN;
    }

    public function setDateN(\DateTimeInterface $dateN): self
    {
        $this->dateN = $dateN;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

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

    /**
     * @return Collection<int, CommentaireNews>
     */
    public function getCommentaireNews(): Collection
    {
        return $this->commentaireNews;
    }

    public function addCommentaireNews(CommentaireNews $commentaireNews): self
    {
        if (!$this->commentaireNews->contains($commentaireNews)) {
            $this->commentaireNews->add($commentaireNews);
            $commentaireNews->setIdNews($this);
        }

        return $this;
    }

    public function removeCommentaireNews(CommentaireNews $commentaireNews): self
    {
        if ($this->commentaireNews->removeElement($commentaireNews)) {
            // set the owning side to null (unless already changed)
            if ($commentaireNews->getIdNews() === $this) {
                $commentaireNews->setIdNews(null);
            }
        }

        return $this;
    }

    public function getIdJeux(): ?Jeux
    {
        return $this->idJeux;
    }


    public function setIdJeux(?Jeux $idJeux): self
    {
        $this->idJeux = $idJeux;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @param string|null $image
     */
    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

}
