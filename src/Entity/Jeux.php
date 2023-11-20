<?php

namespace App\Entity;

use App\Repository\JeuxRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Vonage\Response;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: JeuxRepository::class)]
class Jeux
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("jeux")]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^[a-zA-Z ]+$/', message: 'Le nom du jeu ne doit contenir que des lettres et espaces')]
    #[UniqueEntity(fields: ['nomGame'], message: 'Ce nom de jeu est déjà utilisé')]
    #[ORM\Column(length: 255)]
    #[Groups("jeux")]
    private ?string $nomGame = null;
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups("jeux")]
    private ?\DateTimeInterface $DateAddGame = null;
    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'Le nombre de joueurs doit être supérieur ou égal à {{ compared_value }}')]
    #[ORM\Column]
    #[Groups("jeux")]
    private ?int $maxPlayers = null;

    #[Assert\NotBlank]
    #[Assert\Image(mimeTypes: ['image/jpeg', 'image/png', 'image/gif'], mimeTypesMessage: 'Le fichier doit être une image de type JPEG, PNG ou GIF')]
    #[ORM\Column]
    #[Groups("jeux")]
    private ?string $image = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255)]
    #[Groups("jeux")]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'idJeux', targetEntity: News::class, cascade: ["remove"])]
    private Collection $news;

    #[ORM\OneToMany(mappedBy: 'idJeux', targetEntity: ReviewJeux::class,cascade: ["remove"])]
    private Collection $reviewJeuxes;


    #[ORM\OneToMany(mappedBy: 'idJeux', targetEntity: Cours::class, cascade: ["remove"])]
    private Collection $cours;


    public function __construct()
    {
        //$this->news = new ArrayCollection();
        //$this->reviewJeuxes = new ArrayCollection();
        //$this->cours = new ArrayCollection();
        $this->DateAddGame  = new \DateTime();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomGame(): ?string
    {
        return $this->nomGame;
    }

    public function setNomGame(string $nomGame): self
    {
        $this->nomGame = $nomGame;

        return $this;
    }

    public function getDateAddGame(): ?\DateTimeInterface
    {
        return $this->DateAddGame;
    }

    public function setDateAddGame(\DateTimeInterface $DateAddGame): self
    {
        $this->DateAddGame = $DateAddGame;

        return $this;
    }

    public function getMaxPlayers(): ?int
    {
        return $this->maxPlayers;
    }

    public function setMaxPlayers(int $maxPlayers): self
    {
        $this->maxPlayers = $maxPlayers;

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
     * @return Collection<int, News>
     */
    public function getNews(): Collection
    {
        return $this->news;
    }

    public function addNews(News $news): self
    {
        if (!$this->news->contains($news)) {
            $this->news->add($news);
            $news->setIdJeux($this);
        }

        return $this;
    }

    public function removeNews(News $news): self
    {
        if ($this->news->removeElement($news)) {
            // set the owning side to null (unless already changed)
            if ($news->getIdJeux() === $this) {
                $news->setIdJeux(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ReviewJeux>
     */
    public function getReviewJeuxes(): Collection
    {
        return $this->reviewJeuxes;
    }

    public function addReviewJeux(ReviewJeux $reviewJeux): self
    {
        if (!$this->reviewJeuxes->contains($reviewJeux)) {
            $this->reviewJeuxes->add($reviewJeux);
            $reviewJeux->setIdJeux($this);
        }

        return $this;
    }

    public function removeReviewJeux(ReviewJeux $reviewJeux): self
    {
        if ($this->reviewJeuxes->removeElement($reviewJeux)) {
            // set the owning side to null (unless already changed)
            if ($reviewJeux->getIdJeux() === $this) {
                $reviewJeux->setIdJeux(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cours>
     */
    public function getCours(): Collection
    {
        return $this->cours;
    }

    public function addCour(Cours $cour): self
    {
        if (!$this->cours->contains($cour)) {
            $this->cours->add($cour);
            $cour->setIdJeux($this);
        }

        return $this;
    }

    public function removeCour(Cours $cour): self
    {
        if ($this->cours->removeElement($cour)) {
            // set the owning side to null (unless already changed)
            if ($cour->getIdJeux() === $this) {
                $cour->setIdJeux(null);
            }
        }

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

