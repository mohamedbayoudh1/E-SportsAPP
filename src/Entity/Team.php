<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;




    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom_team = null;


    #[ORM\Column(nullable: true)]
    private ?int $nb_joueurs = null;

    #[ORM\OneToMany(mappedBy: 'idTeam', targetEntity: Classement::class,cascade: ["remove"])]
    private Collection $classements;

    #[ORM\OneToMany(mappedBy: 'idTeam', targetEntity: Membre::class,cascade: ["remove"])]
    private Collection $membres;

    #[ORM\Column(type: Types::TEXT,nullable: true)]
    private ?string $about = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[ORM\ManyToOne(inversedBy: 'teams')]
    private ?Gamer $ownerteam = null;

    #[ORM\Column(nullable: true)]
    private ?int $win = null;

    #[ORM\Column(nullable: true)]
    private ?int $lose = null;



    public function __construct()
    {
        $this->classements = new ArrayCollection();
        $this->membres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getNomTeam(): ?string
    {
        return $this->nom_team;
    }

    public function setNomTeam(string $nom_team): self
    {
        $this->nom_team = $nom_team;

        return $this;
    }

    public function getNbJoueurs(): ?int
    {
        return $this->nb_joueurs;
    }

    public function setNbJoueurs(int $nb_joueurs): self
    {
        $this->nb_joueurs = $nb_joueurs;

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
            $classement->setIdTeam($this);
        }

        return $this;
    }

    public function removeClassement(Classement $classement): self
    {
        if ($this->classements->removeElement($classement)) {
            // set the owning side to null (unless already changed)
            if ($classement->getIdTeam() === $this) {
                $classement->setIdTeam(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Membre>
     */
    public function getMembres(): Collection
    {
        return $this->membres;
    }

    public function addMembre(Membre $membre): self
    {
        if (!$this->membres->contains($membre)) {
            $this->membres->add($membre);
            $membre->setIdTeam($this);
        }

        return $this;
    }

    public function removeMembre(Membre $membre): self
    {
        if ($this->membres->removeElement($membre)) {
            // set the owning side to null (unless already changed)
            if ($membre->getIdTeam() === $this) {
                $membre->setIdTeam(null);
            }
        }

        return $this;
    }

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function setAbout(string $about): self
    {
        $this->about = $about;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }
    public function __toString()
    {
        return (string) $this->id;
    }

    public function getOwnerteam(): ?Gamer
    {
        return $this->ownerteam;
    }

    public function setOwnerteam(?Gamer $ownerteam): self
    {
        $this->ownerteam = $ownerteam;

        return $this;
    }

    public function getWin(): ?int
    {
        return $this->win;
    }

    public function setWin(?int $win): self
    {
        $this->win = $win;

        return $this;
    }

    public function getLose(): ?int
    {
        return $this->lose;
    }

    public function setLose(?int $lose): self
    {
        $this->lose = $lose;

        return $this;
    }


}
