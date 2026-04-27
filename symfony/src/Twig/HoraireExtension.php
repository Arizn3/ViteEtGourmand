<?php

namespace App\Twig;

// Cette classe crée une fonction Twig, qui étend une donnée dans n'importe quelle Vue sans passer par un Controller

use App\Service\HoraireService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

// AbstractExtension crée une Extension Twig
class HoraireExtension extends AbstractExtension
{
    private $horaireService;

    public function __construct(HoraireService $horaireService)
    {
        $this->horaireService = $horaireService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('horaires', [$this, 'getHoraires']),
        ];
    }

    public function getHoraires()
    {
        return $this->horaireService->getHoraires();
    }
}