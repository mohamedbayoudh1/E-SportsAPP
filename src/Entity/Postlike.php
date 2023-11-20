<?php

namespace App\Entity;

use App\Repository\PostlikeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostlikeRepository::class)]
class Postlike
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'postlikes')]
    private ?Post $idPost = null;

    #[ORM\ManyToOne(inversedBy: 'postlikes')]
    private ?Gamer $idgamer = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdPost(): ?Post
    {
        return $this->idPost;
    }

    public function setIdPost(?Post $idPost): self
    {
        $this->idPost = $idPost;

        return $this;
    }

    public function getIdgamer(): ?Gamer
    {
        return $this->idgamer;
    }

    public function setIdgamer(?Gamer $idgamer): self
    {
        $this->idgamer = $idgamer;

        return $this;
    }



}
