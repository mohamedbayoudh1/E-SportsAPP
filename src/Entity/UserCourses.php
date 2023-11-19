<?php

namespace App\Entity;

use App\Repository\UserCoursesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserCoursesRepository::class)]
class UserCourses
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $favori = null;

    #[ORM\Column]
    private ?bool $acheter = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?int $review = null;

    #[ORM\ManyToOne(inversedBy: 'userCourses')]
    private ?Gamer $idGamer = null;

    #[ORM\ManyToOne(inversedBy: 'userCourses')]
    private ?Cours $idCours = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isFavori(): ?bool
    {
        return $this->favori;
    }

    public function setFavori(bool $favori): self
    {
        $this->favori = $favori;

        return $this;
    }

    public function isAcheter(): ?bool
    {
        return $this->acheter;
    }

    public function setAcheter(bool $acheter): self
    {
        $this->acheter = $acheter;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getReview(): ?int
    {
        return $this->review;
    }

    public function setReview(?int $review): self
    {
        $this->review = $review;

        return $this;
    }

    public function getIdGamer(): ?Gamer
    {
        return $this->idGamer;
    }

    public function setIdGamer(?Gamer $idGamer): self
    {
        $this->idGamer = $idGamer;

        return $this;
    }

    public function getIdCours(): ?Cours
    {
        return $this->idCours;
    }

    public function setIdCours(?Cours $idCours): self
    {
        $this->idCours = $idCours;

        return $this;
    }
}
