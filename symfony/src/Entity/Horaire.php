<?php

namespace App\Entity;

use App\Repository\HoraireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HoraireRepository::class)]
class Horaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $jour = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $heureOuverture = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $HeureFermeture = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJour(): ?string
    {
        return $this->jour;
    }

    public function setJour(string $jour): static
    {
        $this->jour = $jour;

        return $this;
    }

    public function getHeureOuverture(): ?\DateTime
    {
        return $this->heureOuverture;
    }

    public function setHeureOuverture(\DateTime $heureOuverture): static
    {
        $this->heureOuverture = $heureOuverture;

        return $this;
    }

    public function getHeureFermeture(): ?\DateTime
    {
        return $this->HeureFermeture;
    }

    public function setHeureFermeture(\DateTime $HeureFermeture): static
    {
        $this->HeureFermeture = $HeureFermeture;

        return $this;
    }
}
