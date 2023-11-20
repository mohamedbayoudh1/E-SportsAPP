<?php

namespace App\Entity;

use App\Repository\CoursRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CoursRepository::class)]
class Cours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("Cours")]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("Cours")]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    #[Groups("Cours")]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("Cours")]
    private ?string $video = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("Cours")]
    private ?string $image = null;

    #[ORM\Column(nullable: true)]
    #[Groups("Cours")]
    private ?int $prix = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("Cours")]
    private ?string $niveau = null;

    #[ORM\ManyToOne(inversedBy: 'cours', cascade: ["remove"])]
    private ?Coach $idCoach = null;

    #[ORM\OneToMany(mappedBy: 'idCours', targetEntity: UserCourses::class ,cascade: ["remove"])]
    private Collection $userCourses;

    #[ORM\ManyToOne(inversedBy: 'cours', cascade: ["remove"])]
    private ?Jeux $idJeux = null;

    #[ORM\Column(type: 'integer')]
    #[Groups("Cours")]
    private ?int $etat = null;

    public function __construct()
    {
        $this->userCourses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(string $video): self
    {
        $this->video = $video;

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

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getNiveau(): ?string
    {
        return $this->niveau;
    }

    public function setNiveau(?string $niveau): self
    {
        $this->niveau = $niveau;

        return $this;
    }

    public function getIdCoach(): ?Coach
    {
        return $this->idCoach;
    }

    public function setIdCoach(?Coach $idCoach): self
    {
        $this->idCoach = $idCoach;
        return $this;
    }

    /**
     * @return Collection<int, UserCourses>
     */
    public function getUserCourses(): Collection
    {
        return $this->userCourses;
    }

    public function addUserCourse(UserCourses $userCourse): self
    {
        if (!$this->userCourses->contains($userCourse)) {
            $this->userCourses->add($userCourse);
            $userCourse->setIdCours($this);
        }

        return $this;
    }

    public function removeUserCourse(UserCourses $userCourse): self
    {
        if ($this->userCourses->removeElement($userCourse)) {
            // set the owning side to null (unless already changed)
            if ($userCourse->getIdCours() === $this) {
                $userCourse->setIdCours(null);
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
