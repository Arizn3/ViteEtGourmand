<?php

namespace App\Entity;

use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestTrait;
use App\Repository\ResetPasswordRequestRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResetPasswordRequestRepository::class)]
class ResetPasswordRequest implements ResetPasswordRequestInterface
{
    use ResetPasswordRequestTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, name: 'utilisateur_id')]
    private ?Utilisateur $user = null;

    public function __construct(Utilisateur $utilisateur, \DateTimeInterface $expiresAt, string $selector, string $hashedToken)
    {
        $this->user = $utilisateur;
        $this->initialize($expiresAt, $selector, $hashedToken);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): Utilisateur
    {
        return $this->user;
    }
}
