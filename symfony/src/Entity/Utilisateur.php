<?php

namespace App\Entity;
# Cette classe servira d'User Symfony pour l'authentification, elle
# a été adapter en y rajoutant les namespaces et leurs méthodes.

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
// Namespace pour Symfony authentification
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
// Namespace pour les contraintes
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // EMAIL
    #[Assert\NotBlank(message: "Un email est nécessaire")]
    #[Assert\Email(message: "Email invalide")]
    #[ORM\Column(length: 50)]
    private ?string $email = null;

    // MOT DE PASSE
    #[ORM\Column(length: 255)]
    private ?string $password = null;

    // PRENOM
    #[Assert\NotBlank(message: "Un prénom est nécessaire")]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: "Le prénom doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le prénom ne peut pas dépasser {{ limit }} caractères"
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-ZÀ-ÿ\s-]+$/",
        message: "Le champ prenom contient des caractères invalides"
    )]
    #[ORM\Column(length: 50)]
    private ?string $prenom = null;

    // NOM
    #[Assert\NotBlank(message: "Un nom est nécessaire")]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères"
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-ZÀ-ÿ\s-]+$/",
        message: "Le champ nom contient des caractères invalides"
    )]
    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    // TELEPHONE
    #[Assert\NotBlank(message: "Le numéro de téléphone est obligatoire")]
    #[Assert\Length(
        min: 10,
        max: 15,
        minMessage: "Le numéro de téléphone doit contenir au moins {{ limit }} chiffres",
        maxMessage: "Le numéro de téléphone ne peut pas dépasser {{ limit }} chiffres"
    )]
    #[Assert\Regex(
        pattern: "/^[0-9+\s]+$/",
        message: "Le téléphone ne doit contenir que des chiffres"
    )]
    #[ORM\Column(length: 50)]
    private ?string $telephone = null;

    // ADRESSE
    #[Assert\NotBlank(message: "L'adresse est obligatoire")]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z0-9À-ÿ\s,.'-]+$/",
        message: "Adresse invalide"
    )]
    #[ORM\Column(length: 50)]
    private ?string $adresse = null;

    // RÔLE
    #[ORM\ManyToOne(inversedBy: 'utilisateurs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Role $role = null;

    #[ORM\OneToOne(mappedBy: 'utilisateur', targetEntity: Avis::class)]
    private ?Avis $avis = null;

    /**
     * @var Collection<int, Commande>
     */
    #[ORM\OneToMany(targetEntity: Commande::class, mappedBy: 'utilisateur')]
    private Collection $commandes;

    public function __construct()
    {
        $this->commandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getAvis(): ?Avis
    {
        return $this->avis;
    }

    public function setAvis(?Avis $avis): self
{
    $this->avis = $avis;

    if ($avis && $avis->getUtilisateur() !== $this) {
        $avis->setUtilisateur($this);
    }

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
            $commande->setUtilisateur($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getUtilisateur() === $this) {
                $commande->setUtilisateur(null);
            }
        }

        return $this;
    }

    // Récupération d'un identifiant pour Symfony 
    public function getUserIdentifier(): string
    {
        // La propriété 'email' de la table est utiliser comme identifiant Symfony
        return $this->email;
    }

    // Récupération des Rôles
    public function getRoles(): array
    {
        // On fait appel au Getter de la propriété Role initialement crée dans cette table
        if ($this->getRole()) {
            return [$this->getRole()->getDescription()];
        }
        // Fallback (si aucun rôle n'est trouvé)
        return ['ROLE_USER'];
    }

    // Supprime les données sensibles gardées en mémoire lors d'une connexion
    // Cette méthode est obligatoire pour le bon fonctionnement des namespaces
    public function eraseCredentials(): void {}
}
