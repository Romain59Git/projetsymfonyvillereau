<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Rencontre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $equipe;

    #[ORM\Column(type: 'string', length: 255)]
    private $adversaire;

    #[ORM\Column(type: 'date')]
    private $date;

    #[ORM\Column(type: 'string', length: 255)]
    private $lieu;

    #[ORM\Column(type: 'string', length: 10)]
    private $heure;

    #[ORM\ManyToMany(targetEntity: Licencie::class)]
    #[ORM\JoinTable(name: 'rencontre_licencie')]
    private Collection $joueurs;

    public function __construct()
    {
        $this->joueurs = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getEquipe(): ?string { return $this->equipe; }
    public function setEquipe(?string $equipe): self { $this->equipe = $equipe; return $this; }
    public function getAdversaire(): ?string { return $this->adversaire; }
    public function setAdversaire(string $adversaire): self { $this->adversaire = $adversaire; return $this; }
    public function getDate(): ?\DateTimeInterface { return $this->date; }
    public function setDate(\DateTimeInterface $date): self { $this->date = $date; return $this; }
    public function getLieu(): ?string { return $this->lieu; }
    public function setLieu(string $lieu): self { $this->lieu = $lieu; return $this; }
    public function getHeure(): ?string { return $this->heure; }
    public function setHeure(string $heure): self { $this->heure = $heure; return $this; }

    /**
     * @return Collection<int, Licencie>
     */
    public function getJoueurs(): Collection
    {
        return $this->joueurs;
    }

    public function addJoueur(Licencie $joueur): self
    {
        if (!$this->joueurs->contains($joueur)) {
            $this->joueurs->add($joueur);
        }
        return $this;
    }

    public function removeJoueur(Licencie $joueur): self
    {
        $this->joueurs->removeElement($joueur);
        return $this;
    }
} 