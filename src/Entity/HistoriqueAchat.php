<?php

namespace App\Entity;

use App\Repository\HistoriqueAchatRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoriqueAchatRepository::class)]
class HistoriqueAchat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;



    #[ORM\Column(type: Types::DATE_MUTABLE,nullable: true)]
    private ?\DateTimeInterface $date_dachat = null;

    #[ORM\ManyToOne(inversedBy: 'historiqueAchats')]
    private ?Gamer $id_gamer = null;

    #[ORM\ManyToOne(inversedBy: 'historiqueAchats')]
    private ?Produit $idproduit = null;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $reference = null;

    #[ORM\Column]
    private ?bool $etatachat = null;

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getDateDachat(): ?\DateTimeInterface
    {
        return $this->date_dachat;
    }

    public function setDateDachat(\DateTimeInterface $date_dachat): self
    {
        $this->date_dachat = $date_dachat;

        return $this;
    }

    public function getIdGamer(): ?Gamer
    {
        return $this->id_gamer;
    }

    public function setIdGamer(?Gamer $id_gamer): self
    {
        $this->id_gamer = $id_gamer;

        return $this;
    }

    public function getIdproduit(): ?Produit
    {
        return $this->idproduit;
    }

    public function setIdproduit(?Produit $idproduit): self
    {
        $this->idproduit = $idproduit;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function isEtatachat(): ?bool
    {
        return $this->etatachat;
    }

    public function setEtatachat(bool $etatachat): self
    {
        $this->etatachat = $etatachat;

        return $this;
    }
}
