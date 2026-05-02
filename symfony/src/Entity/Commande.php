<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateCmd = null;

    #[Assert\NotNull]
    #[Assert\Type(\DateTime::class)]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $datePrestation = null;

    #[Assert\NotNull]
    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $heureLivraison = null;

    #[ORM\Column]
    private ?float $prixMenu = null;

    #[Assert\NotNull]
    #[Assert\Positive]
    #[ORM\Column]
    private ?int $nbPersonne = null;

    #[ORM\Column]
    private ?float $prixLivraison = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = null;

    #[ORM\Column]
    private ?bool $pretMateriel = null;

    #[ORM\Column]
    private ?bool $restitutionMateriel = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Menu $menu = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255)]
    private ?string $adresseLivraison = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 50)]
    private ?string $villeLivraison = null;

    /**
     * @var Collection<int, CommandeHistorique>
     */
    #[ORM\OneToMany(targetEntity: CommandeHistorique::class, mappedBy: 'commande', orphanRemoval: true)]
    private Collection $historiques;

    public function __construct()
    {
        $this->historiques = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCmd(): ?\DateTime
    {
        return $this->dateCmd;
    }

    public function setDateCmd(\DateTime $dateCmd): static
    {
        $this->dateCmd = $dateCmd;

        return $this;
    }

    public function getDatePrestation(): ?\DateTime
    {
        return $this->datePrestation;
    }

    public function setDatePrestation(\DateTime $datePrestation): static
    {
        $this->datePrestation = $datePrestation;

        return $this;
    }

    public function getHeureLivraison(): ?\DateTime
    {
        return $this->heureLivraison;
    }

    public function setHeureLivraison(\DateTime $heureLivraison): static
    {
        $this->heureLivraison = $heureLivraison;

        return $this;
    }

    public function getPrixMenu(): ?float
    {
        return $this->prixMenu;
    }

    public function setPrixMenu(float $prixMenu): static
    {
        $this->prixMenu = $prixMenu;

        return $this;
    }

    public function getNbPersonne(): ?int
    {
        return $this->nbPersonne;
    }

    public function setNbPersonne(int $nbPersonne): static
    {
        $this->nbPersonne = $nbPersonne;

        return $this;
    }

    public function getPrixLivraison(): ?float
    {
        return $this->prixLivraison;
    }

    public function setPrixLivraison(float $prixLivraison): static
    {
        $this->prixLivraison = $prixLivraison;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function isPretMateriel(): ?bool
    {
        return $this->pretMateriel;
    }

    public function setPretMateriel(bool $pretMateriel): static
    {
        $this->pretMateriel = $pretMateriel;

        return $this;
    }

    public function isRestitutionMateriel(): ?bool
    {
        return $this->restitutionMateriel;
    }

    public function setRestitutionMateriel(bool $restitutionMateriel): static
    {
        $this->restitutionMateriel = $restitutionMateriel;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getMenu(): ?Menu
    {
        return $this->menu;
    }

    public function setMenu(?Menu $menu): static
    {
        $this->menu = $menu;

        return $this;
    }

    public function getAdresseLivraison(): ?string
    {
        return $this->adresseLivraison;
    }

    public function setAdresseLivraison(string $adresseLivraison): static
    {
        $this->adresseLivraison = $adresseLivraison;
        return $this;
    }

    public function getVilleLivraison(): ?string
    {
        return $this->villeLivraison;
    }

    public function setVilleLivraison(string $villeLivraison): static
    {
        $this->villeLivraison = $villeLivraison;
        return $this;
    }

    /**
     * @return Collection<int, CommandeHistorique>
     */
    public function getHistoriques(): Collection
    {
        return $this->historiques;
    }

    public function addHistorique(CommandeHistorique $historique): static
    {
        if (!$this->historiques->contains($historique)) {
            $this->historiques->add($historique);
            $historique->setCommande($this);
        }

        return $this;
    }

    public function removeHistorique(CommandeHistorique $historique): static
    {
        if ($this->historiques->removeElement($historique)) {
            // set the owning side to null (unless already changed)
            if ($historique->getCommande() === $this) {
                $historique->setCommande(null);
            }
        }

        return $this;
    }
}
