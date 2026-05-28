<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use DateTime;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
class Menu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank()]
    #[Assert\Length(min: 2, max: 30)]
    #[Assert\Regex(
        pattern: "/<script>/i",
        match: false,
        message: "Caractères invalides."
    )]
    #[ORM\Column(length: 50)]
    private ?string $titre = null;

    #[Assert\NotBlank()]
    #[Assert\Length(min: 1, max: 300)]
    #[ORM\Column]
    private ?int $personneMini = null;

    #[Assert\NotBlank()]
    #[Assert\Positive()]
    #[Assert\Length(min: 1, max: 300)]
    #[ORM\Column]
    private ?float $prixPersonne = null;

    #[Assert\NotBlank()]
    #[Assert\Length(
        min: 1,
        max: 1000,
        maxMessage: 'La description ne peut pas dépasser 1000 caractères.'
    )]
    #[Assert\Regex(
        pattern: "/<script>/i",
        match: false,
        message: "Caractères invalides."
    )]
    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[Assert\NotBlank()]
    #[Assert\PositiveOrZero()]
    #[ORM\Column]
    private ?int $qttRestante = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $deletedAt = null;

    #[ORM\ManyToOne(inversedBy: 'menus')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Theme $theme = null;

    #[ORM\ManyToOne(inversedBy: 'menus')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Regime $regime = null;

    #[ORM\ManyToMany(targetEntity: Plat::class, inversedBy: 'menus')]
    #[ORM\JoinColumn(nullable: false)]
    private Collection $plats;

    /**
     * @var Collection<int, Commande>
     */
    #[ORM\OneToMany(targetEntity: Commande::class, mappedBy: 'menu')]
    private Collection $commandes;

    public function __construct()
    {
        $this->commandes = new ArrayCollection();
        $this->plats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getPersonneMini(): ?int
    {
        return $this->personneMini;
    }

    public function setPersonneMini(int $personneMini): static
    {
        $this->personneMini = $personneMini;

        return $this;
    }

    public function getPrixPersonne(): ?float
    {
        return $this->prixPersonne;
    }

    public function setPrixPersonne(float $prixPersonne): static
    {
        $this->prixPersonne = $prixPersonne;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getQttRestante(): ?int
    {
        return $this->qttRestante;
    }

    public function setQttRestante(int $qttRestante): static
    {
        $this->qttRestante = $qttRestante;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTime $deletedAt): static
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getTheme(): ?Theme
    {
        return $this->theme;
    }

    public function setTheme(?Theme $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    public function getRegime(): ?Regime
    {
        return $this->regime;
    }

    public function setRegime(?Regime $regime): static
    {
        $this->regime = $regime;

        return $this;
    }

    /**
     * @return Collection<int, Plat>
     */
    public function getPlats(): Collection
    {
        return $this->plats;
    }

    public function addPlat(Plat $plat): static
    {
        if (!$this->plats->contains($plat)) {
            $this->plats[] = $plat;
        }

        return $this;
    }

    public function removePlat(Plat $plat): static
    {
        $this->plats->removeElement($plat);

        return $this;
    }

    /**
     * @return Collection<int, Commande>
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): static
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setMenu($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getMenu() === $this) {
                $commande->setMenu(null);
            }
        }

        return $this;
    }
}
