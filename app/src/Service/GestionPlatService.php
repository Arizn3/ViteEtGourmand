<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PlatRepository;
use App\Entity\Menu;
use App\Entity\Plat;

class GestionPlatService
{
    public function __construct(
        private EntityManagerInterface $em,
        private SluggerInterface $slugger,
        #[Autowire('%image_directory%')]
        private string $imageDirectory,
        private PlatRepository $platRepo,
    ) {}

    public function affichagePlat()
    {
        return $this->platRepo->findBy([
            'deletedAt' => null
        ]);
    }

    public function nouveauPlat(UploadedFile $photo, Plat $plat)
    {
        if ($photo) {
            $nomOriginal = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
            $safeNom = $this->slugger->slug($nomOriginal);
            $nouveauNom = $safeNom . '-' . uniqid() . '.' . $photo->guessExtension();
            $photo->move(
                $this->imageDirectory,
                $nouveauNom
            );
            $plat->setPhoto($nouveauNom);
        }
        $plat->setCreatedAt(new \DateTime());
        $this->em->persist($plat);
        $this->em->flush();
    }

    public function desactiverPlat(Plat $plat)
    {
        // Condition pour la vérification d'une relation entre un plat et un menu
        $menusActifs = $plat->getMenus()->filter(function ($menu) {
            return $menu->getDeletedAt() === null;
        });
        if (!$menusActifs->isEmpty()) {
            throw new \RuntimeException(
                $plat->getNomPlat() . ' est impossible à supprimer, ce plat est utilisé dans un menu.'
            );
        };
        $photo = $plat->getPhoto();
        if ($photo) {
            $pathPhoto = $this->imageDirectory . '/' . $photo;
            if (file_exists($pathPhoto)) {
                // Supprime la photo du fichier uploads/plat
                unlink($pathPhoto);
            };
        };
        $plat->setDeletedAt(new \DateTime());
        $this->em->flush();
    }
}
