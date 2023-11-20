<?php

namespace App\Entity;

use App\Repository\GamerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GamerRepository::class)]
class Gamer extends User
{
    
    #[ORM\Column(length: 255)]
    private ?string $tag = null;

    #[ORM\OneToMany(mappedBy: 'id_gamer', targetEntity: HistoriqueAchat::class, cascade: ["remove"])]
    private Collection $historiqueAchats;

    #[ORM\OneToMany(mappedBy: 'idGamer', targetEntity: Planning::class, cascade: ["remove"])]
    private Collection $plannings;

    #[ORM\OneToMany(mappedBy: 'idGamer', targetEntity: UserCourses::class, cascade: ["remove"])]
    private Collection $userCourses;

    #[ORM\OneToMany(mappedBy: 'idGamer', targetEntity: Membre::class, cascade: ["remove"])]
    private Collection $membres;

    #[ORM\OneToMany(mappedBy: 'idGamer', targetEntity: ReviewJeux::class, cascade: ["remove"])]
    private Collection $reviewJeuxes;

    #[ORM\OneToMany(mappedBy: 'idGamer', targetEntity: MembreGroupe::class, cascade: ["remove"])]
    private Collection $membreGroupes;

    #[ORM\OneToMany(mappedBy: 'ownerteam', targetEntity: Team::class)]
    private Collection $teams;

    #[ORM\OneToMany(mappedBy: 'ownertournoi', targetEntity: Tournoi::class)]
    private Collection $tournois;


    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Notif::class)]
    private Collection $notifs;


    #[ORM\OneToMany(mappedBy: 'idgamer', targetEntity: Postlike::class)]
    private Collection $postlikes;


    public function __construct()
    {
        $this->historiqueAchats = new ArrayCollection();
        $this->plannings = new ArrayCollection();
        $this->userCourses = new ArrayCollection();
        $this->membres = new ArrayCollection();
        $this->reviewJeuxes = new ArrayCollection();
        $this->membreGroupes = new ArrayCollection();
        $this->teams = new ArrayCollection();
        $this->tournois = new ArrayCollection();
        $this->notifs = new ArrayCollection();
        $this->postlikes = new ArrayCollection();

    }
    

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * @return Collection<int, HistoriqueAchat>
     */
    public function getHistoriqueAchats(): Collection
    {
        return $this->historiqueAchats;
    }

    public function addHistoriqueAchat(HistoriqueAchat $historiqueAchat): self
    {
        if (!$this->historiqueAchats->contains($historiqueAchat)) {
            $this->historiqueAchats->add($historiqueAchat);
            $historiqueAchat->setIdGamer($this);
        }

        return $this;
    }

    public function removeHistoriqueAchat(HistoriqueAchat $historiqueAchat): self
    {
        if ($this->historiqueAchats->removeElement($historiqueAchat)) {
            // set the owning side to null (unless already changed)
            if ($historiqueAchat->getIdGamer() === $this) {
                $historiqueAchat->setIdGamer(null);
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
            $planning->setIdGamer($this);
        }

        return $this;
    }

    public function removePlanning(Planning $planning): self
    {
        if ($this->plannings->removeElement($planning)) {
            // set the owning side to null (unless already changed)
            if ($planning->getIdGamer() === $this) {
                $planning->setIdGamer(null);
            }
        }

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
            $userCourse->setIdGamer($this);
        }

        return $this;
    }

    public function removeUserCourse(UserCourses $userCourse): self
    {
        if ($this->userCourses->removeElement($userCourse)) {
            // set the owning side to null (unless already changed)
            if ($userCourse->getIdGamer() === $this) {
                $userCourse->setIdGamer(null);
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
            $membre->setIdGamer($this);
        }

        return $this;
    }

    public function removeMembre(Membre $membre): self
    {
        if ($this->membres->removeElement($membre)) {
            // set the owning side to null (unless already changed)
            if ($membre->getIdGamer() === $this) {
                $membre->setIdGamer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ReviewJeux>
     */
    public function getReviewJeuxes(): Collection
    {
        return $this->reviewJeuxes;
    }

    /**
     * @return Collection<int, MembreGroupe>
     */
    public function getMembreGroupes(): Collection
    {
        return $this->membreGroupes;
    }

    public function addMembreGroupe(MembreGroupe $membreGroupe): self
    {
        if (!$this->membreGroupes->contains($membreGroupe)) {
            $this->membreGroupes->add($membreGroupe);
            $membreGroupe->setIdGamer($this);
        }

        return $this;
    }

    public function removeMembreGroupe(MembreGroupe $membreGroupe): self
    {
        if ($this->membreGroupes->removeElement($membreGroupe)) {
            // set the owning side to null (unless already changed)
            if ($membreGroupe->getIdGamer() === $this) {
                $membreGroupe->setIdGamer(null);
            }
        }

        return $this;
    }



    /**
     * @return Collection<int, Team>
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): self
    {
        if (!$this->teams->contains($team)) {
            $this->teams->add($team);
            $team->setOwnerteam($this);
        }

        return $this;
    }

    public function removeTeam(Team $team): self
    {
        if ($this->teams->removeElement($team)) {
            // set the owning side to null (unless already changed)
            if ($team->getOwnerteam() === $this) {
                $team->setOwnerteam(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tournoi>
     */
    public function getTournois(): Collection
    {
        return $this->tournois;
    }

    public function addTournoi(Tournoi $tournoi): self
    {
        if (!$this->tournois->contains($tournoi)) {
            $this->tournois->add($tournoi);
            $tournoi->setOwnertournoi($this);
        }

        return $this;
    }

    public function removeTournoi(Tournoi $tournoi): self
    {
        if ($this->tournois->removeElement($tournoi)) {
            // set the owning side to null (unless already changed)
            if ($tournoi->getOwnertournoi() === $this) {
                $tournoi->setOwnertournoi(null);
            }
        }

        return $this;
    }

    /**

     * @return Collection<int, Notif>
     */
    public function getNotifs(): Collection
    {
        return $this->notifs;
    }

    public function addNotif(Notif $notif): self
    {
        if (!$this->notifs->contains($notif)) {
            $this->notifs->add($notif);
            $notif->setOwner($this);
        }
        return $this;
    }

/**
     * @return Collection<int, Postlike>
     */
    public function getPostlikes(): Collection
    {
        return $this->postlikes;
    }

    public function addPostlike(Postlike $postlike): self
    {
        if (!$this->postlikes->contains($postlike)) {
            $this->postlikes->add($postlike);
            $postlike->setIdgamer($this);

        }

        return $this;
    }


    public function removeNotif(Notif $notif): self
    {
        if ($this->notifs->removeElement($notif)) {
            // set the owning side to null (unless already changed)
            if ($notif->getOwner() === $this) {
                $notif->setOwner(null);
            }
           
        } return $this;
    }

    public function removePostlike(Postlike $postlike): self
    {
        if ($this->postlikes->removeElement($postlike)) {
            // set the owning side to null (unless already changed)
            if ($postlike->getIdgamer() === $this) {
                $postlike->setIdgamer(null);

            }
        }

        return $this;
    }

}
