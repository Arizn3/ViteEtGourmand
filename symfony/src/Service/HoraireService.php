<?php

namespace App\Service;

// Cette classe va permettre de transmettre les données de l'Entité Horaire au fichier Twig footer.html.twig
// en passant par le fichier Twig/HoraireExtension.php.

use App\Repository\HoraireRepository;

class HoraireService
{
    private $horaireRepo;

    // Méthode qui utilise le fichier HoraireRepository de l'Entité Horaire
    public function __construct(HoraireRepository $horaireRepo)
    {
        $this->horaireRepo = $horaireRepo;
    }

    // Agit comme un getter
    public function getHoraires()
    {
        return $this->horaireRepo->findAll();
    }
};