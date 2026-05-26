<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Attribute as MongoDB;

#[MongoDB\Document]
class Stat
{
    #[MongoDB\Id]
    private ?string $id;

    #[MongoDB\Field(type: "string")]
    private string $menu;

    #[MongoDB\Field(type: "int")]
    private int $totalCommandes = 0;

    #[MongoDB\Field(type: "float")]
    private float $chiffreAffaire = 0;

    #[MongoDB\Field(type: "date")]
    private \DateTime $date;

    #[MongoDB\Field(type: "string")]
    private ?string $periode = null;

    public function getPeriode(): ?string
    {
        return $this->periode;
    }

    public function setPeriode(string $periode): self
    {
        $this->periode = $periode;

        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getMenu(): string
    {
        return $this->menu;
    }

    public function setMenu(string $menu): self
    {
        $this->menu = $menu;
        return $this;
    }

    public function getTotalCommandes(): int
    {
        return $this->totalCommandes;
    }

    public function setTotalCommandes(int $totalCommandes): self
    {
        $this->totalCommandes = $totalCommandes;
        return $this;
    }

    public function getChiffreAffaire(): float
    {
        return $this->chiffreAffaire;
    }

    public function setChiffreAffaire(float $chiffreAffaire): self
    {
        $this->chiffreAffaire = $chiffreAffaire;
        return $this;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;
        return $this;
    }
}
