<?php

namespace App\Entity;

use App\Repository\NotifRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotifRepository::class)]
class Notif
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $contenet = null;

    #[ORM\ManyToOne(inversedBy: 'notifs')]
    private ?Gamer $owner = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenet(): ?string
    {
        return $this->contenet;
    }

    public function setContenet(?string $contenet): self
    {
        $this->contenet = $contenet;

        return $this;
    }

    public function getOwner(): ?Gamer
    {
        return $this->owner;
    }

    public function setOwner(?Gamer $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
