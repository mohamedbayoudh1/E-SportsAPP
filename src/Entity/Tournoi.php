<?php

namespace App\Entity;

use App\Repository\TournoiRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TournoiRepository::class)]
class Tournoi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("Tournoi")]
    private ?int $id = null;
    #[Groups("Tournoi")]
    #[ORM\Column(nullable: true)]
    private ?int $nb_team = null;
    #[Groups("Tournoi")]
    #[ORM\Column(nullable: true)]
    private ?int $nb_joueur_team = null;

    #[ORM\OneToMany(mappedBy: 'idTournois', targetEntity: Classement::class,cascade: ["remove"])]
    private Collection $classements;
    #[Groups("Tournoi")]
    #[ORM\Column(length: 255,nullable: true)]
    private ?string $nomtournoi = null;
    #[Groups("Tournoi")]
    #[ORM\Column(length: 255,nullable: true)]
    private ?string $device = null;
    #[Groups("Tournoi")]
    #[ORM\Column(nullable: true)]
    private ?\DateTime $datestart = null;
    #[Groups("Tournoi")]
    #[ORM\Column(length: 255,nullable: true)]
    private ?string $image = null;

    #[Groups("Tournoi")]
    #[ORM\Column(nullable: true)]
    private ?int $etat = null;

    #[ORM\ManyToOne(inversedBy: 'tournois')]
    private ?Gamer $ownertournoi = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbParticipant = null;

    public function __construct()
    {
        $this->classements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbTeam(): ?int
    {
        return $this->nb_team;
    }

    public function setNbTeam(int $nb_team): self
    {
        $this->nb_team = $nb_team;

        return $this;
    }

    public function getNbJoueurTeam(): ?int
    {
        return $this->nb_joueur_team;
    }

    public function setNbJoueurTeam(int $nb_joueur_team): self
    {
        $this->nb_joueur_team = $nb_joueur_team;

        return $this;
    }

    /**
     * @return Collection<int, Classement>
     */
    public function getClassements(): Collection
    {
        return $this->classements;
    }

    public function addClassement(Classement $classement): self
    {
        if (!$this->classements->contains($classement)) {
            $this->classements->add($classement);
            $classement->setIdTournois($this);
        }

        return $this;
    }

    public function removeClassement(Classement $classement): self
    {
        if ($this->classements->removeElement($classement)) {
            // set the owning side to null (unless already changed)
            if ($classement->getIdTournois() === $this) {
                $classement->setIdTournois(null);
            }
        }

        return $this;
    }

    public function getNomtournoi(): ?string
    {
        return $this->nomtournoi;
    }

    public function setNomtournoi(string $nomtournoi): self
    {
        $this->nomtournoi = $nomtournoi;

        return $this;
    }

    public function getDevice(): ?string
    {
        return $this->device;
    }

    public function setDevice(string $device): self
    {
        $this->device = $device;

        return $this;
    }

    public function getDatestart(): ?\DateTime
    {
        return $this->datestart;
    }

    public function setDatestart(\DateTime $datestart): self
    {
        $this->datestart = $datestart;

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


    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(?int $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getOwnertournoi(): ?Gamer
    {
        return $this->ownertournoi;
    }

    public function setOwnertournoi(?Gamer $ownertournoi): self
    {
        $this->ownertournoi = $ownertournoi;

        return $this;
    }

    public function getNbParticipant(): ?int
    {
        return $this->nbParticipant;
    }

    public function setNbParticipant(?int $nbParticipant): self
    {
        $this->nbParticipant = $nbParticipant;

        return $this;
    }

}
