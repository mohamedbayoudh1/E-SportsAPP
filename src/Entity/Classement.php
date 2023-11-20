<?php

namespace App\Entity;

use App\Repository\ClassementRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClassementRepository::class)]
class Classement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(nullable: true)]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?float $score = null;

    #[ORM\ManyToOne(inversedBy: 'classements')]
    private ?Tournoi $idTournois = null;

    #[ORM\ManyToOne( inversedBy: 'classements')]
    private ?Team $idTeam = null;

    #[ORM\Column(nullable: true)]
    private ?int $etat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function setScore(float $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getIdTournois(): ?Tournoi
    {
        return $this->idTournois;
    }

    public function setIdTournois(?Tournoi $idTournois): self
    {
        $this->idTournois = $idTournois;

        return $this;
    }

    public function getIdTeam(): ?Team
    {
        return $this->idTeam;
    }

    public function setIdTeam(?Team $idTeam): self
    {
        $this->idTeam = $idTeam;

        return $this;
    }
    public function setGamer($gamer)
    {
        $this->gamer = $gamer;
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



}
