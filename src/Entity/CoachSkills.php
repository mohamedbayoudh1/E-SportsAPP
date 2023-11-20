<?php

namespace App\Entity;

use App\Repository\CoachSkillsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoachSkillsRepository::class)]
class CoachSkills
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $niveau = null;

    #[ORM\ManyToOne(inversedBy: 'coachSkills')]
    private ?Coach $coach = null;

    #[ORM\ManyToOne(inversedBy: 'coachSkills')]
    private ?Jeux $Jeux = null;

    #[ORM\OneToMany(mappedBy: 'coachSkills', targetEntity: Planning::class)]
    private Collection $plannings;

    public function __construct()
    {
        $this->plannings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCoach(): ?Coach
    {
        return $this->coach;
    }

    public function setCoach(?Coach $coach): self
    {
        $this->coach = $coach;

        return $this;
    }

    public function getJeux(): ?Jeux
    {
        return $this->Jeux;
    }

    public function setJeux(?Jeux $Jeux): self
    {
        $this->Jeux = $Jeux;

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
            $planning->setCoachSkills($this);
        }

        return $this;
    }

    public function removePlanning(Planning $planning): self
    {
        if ($this->plannings->removeElement($planning)) {
            // set the owning side to null (unless already changed)
            if ($planning->getCoachSkills() === $this) {
                $planning->setCoachSkills(null);
            }
        }

        return $this;
    }
}
