<?php

namespace App\Entity;

use App\Repository\CoachRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoachRepository::class)]
class Coach extends User
{
    

    

    #[ORM\Column(nullable: true)]
    private ?float $review = null;

    #[ORM\Column]
    private ?float $prix_heure = null;

    #[ORM\Column(length: 255)]
    private ?string $cv = null;

    #[ORM\OneToMany(mappedBy: 'idCoach', targetEntity: Cours::class)]
    private Collection $cours;

    #[ORM\OneToMany(mappedBy: 'idCoach', targetEntity: Planning::class, cascade: ["remove"])]
    private Collection $plannings;

    #[ORM\Column]
    private ?bool $approuver = null;

    public function __construct()
    {
        $this->cours = new ArrayCollection();
        $this->plannings = new ArrayCollection();
    }

   
    public function getReview(): ?float
    {
        return $this->review;
    }

    public function setReview(?float $review): self
    {
        $this->review = $review;

        return $this;
    }

    public function getPrixHeure(): ?float
    {
        return $this->prix_heure;
    }

    public function setPrixHeure(float $prix_heure): self
    {
        $this->prix_heure = $prix_heure;

        return $this;
    }

    public function getCv(): ?string
    {
        return $this->cv;
    }

    public function setCv(string $cv): self
    {
        $this->cv = $cv;

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
            $cour->setIdCoach($this);
        }

        return $this;
    }

    public function removeCour(Cours $cour): self
    {
        if ($this->cours->removeElement($cour)) {
            // set the owning side to null (unless already changed)
            if ($cour->getIdCoach() === $this) {
                $cour->setIdCoach(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Planning>
     */
    public function getPlannings(): Collection
    {
        return $this->plannings;
    }

    public function addPlanning(Planning $planning): self
    {
        if (!$this->plannings->contains($planning)) {
            $this->plannings->add($planning);
            $planning->setIdCoach($this);
        }

        return $this;
    }

    public function removePlanning(Planning $planning): self
    {
        if ($this->plannings->removeElement($planning)) {
            // set the owning side to null (unless already changed)
            if ($planning->getIdCoach() === $this) {
                $planning->setIdCoach(null);
            }
        }

        return $this;
    }

    public function isApprouver(): ?bool
    {
        return $this->approuver;
    }

    public function setApprouver(bool $approuver): self
    {
        $this->approuver = $approuver;

        return $this;
    }
}
