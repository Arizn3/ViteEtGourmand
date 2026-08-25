<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Service\AffichageCommandeService;
use App\Service\ChangementStatutService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\HoraireRepository;
use App\Repository\RegimeRepository;
use App\Repository\ThemeRepository;
use App\Service\GestionAvisService;
use App\Repository\AvisRepository;
use App\Repository\MenuRepository;
use App\Repository\PlatRepository;
use App\Entity\Utilisateur;
use App\Form\HoraireType;
use App\Entity\Commande;
use App\Form\RegimeType;
use App\Form\ThemeType;
use App\Entity\Horaire;
use App\Form\MenuType;
use App\Entity\Regime;
use App\Form\PlatType;
use App\Entity\Theme;
use App\Entity\Menu;
use App\Entity\Plat;
use App\Entity\Avis;

// Contrôleur pour l'espace employé
final class EmployeController extends AbstractController
{
    // Affichage des commandes pour la gestion des commandes
    #[Route('/employe/commandes', name: 'app_employe_commande')]
    public function index(
        AffichageCommandeService $affichageCommandeService,
        Request $request
    ): Response {
        // Contrôle d'accès
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        // Variables en cas de filtre (commandes actives)
        $idFilter = $request->query->get('idFilter');
        $emailFilter = $request->query->get('email');
        $statutFilter = $request->query->get('statut');
        $commandes = $affichageCommandeService->getFiltreCommandesActives(
            $idFilter,
            $emailFilter,
            $statutFilter
        );

        // Variables en cas de filtre (commandes terminées ou annulées)
        $idTerminer = $request->query->get('idTerminer');
        $emailTerminer = $request->query->get('emailTerminer');
        $statutTerminer = $request->query->get('statutTerminer');
        $commandesTerminees = $affichageCommandeService->getFiltreCommandesTerminer(
            $idTerminer,
            $emailTerminer,
            $statutTerminer,
        );

        return $this->render('employe/index.html.twig', [
            'commandes' => $commandes,
            'commandesTerminees' => $commandesTerminees,
            'commandesActives' => $commandes
        ]);
    }

    // Affichage de l'adresse de livraison d'une commande
    #[Route('/employe/adresse/{id}', name: 'app_adresse_livraison')]
    public function adresseLivraison(Commande $commande): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');
        return $this->render('employe/adresse.html.twig', [
            'commande' => $commande
        ]);
    }

    // Affichage d'information sur le client d'une commande
    #[Route('/employe/utilisateur/{id}', name: 'app_utilisateur')]
    public function detailUtilisateur(Utilisateur $utilisateur): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');
        return $this->render('employe/utilisateur.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    // Page de changement du statut d'une commande
    #[Route('/employe/statut/{id}', name: 'app_commande_statut')]
    public function changementStatut(
        ChangementStatutService $changementStatutService,
        Commande $commande,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        if ($request->isMethod('POST')) {
            $statut = $request->request->get('statut');
            $messageEmail = $request->request->get('message_email');
            $changementStatutService->changementStatut($commande, $messageEmail, $statut);
            return $this->redirectToRoute('app_employe_commande', $request->query->all());
        };

        return $this->render('employe/statut.html.twig', [
            'commande' => $commande,
        ]);
    }

    // Liste des avis utilisateurs
    #[Route('/employe/avis', name: 'app_employe_avis')]
    public function avisUtilisateur(AvisRepository $avisRepo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');
        $avis = $avisRepo->findBy(['statut' => 'EN_ATTENTE']);
        return $this->render('employe/avis.html.twig', [
            'avis' => $avis,
        ]);
    }

    // Function gestion des avis
    #[Route('/employe/avis/{id}/{action}', name: 'app_avis_action')]
    public function actionAvis(
        GestionAvisService $gestionAvis,
        String $action,
        Avis $avis,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');
        try {
            $gestionAvis->gestionAvis($avis, $action);
        } catch (\InvalidArgumentException $e) {
            throw $this->createNotFoundException();
        }
        return $this->redirectToRoute('app_employe_avis');
    }

    // Historique des avis
    #[Route('/employe/avis/historique', name: 'app_historique_avis')]
    public function avisHistorique(AvisRepository $avisRepo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        $avis = $avisRepo->findBy(['statut' => ['VALIDE', 'REFUSER']]);

        return $this->render('employe/historique.html.twig', [
            'avis' => $avis,
        ]);
    }

    // Liste des menus actifs
    #[Route('/employe/gestion-menu', name: 'app_employe_gestion_menu')]
    public function gestionMenu(MenuRepository $menuRepo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        // Commandes qui n'ont pas de valeur dans 'deletedAt'
        $menus = $menuRepo->findBy([
            'deletedAt' => null
        ]);

        return $this->render('employe/gestion-menu.html.twig', [
            'menus' => $menus,
        ]);
    }

    // Création d'un nouveau menu
    #[Route('employe/nouveau-menu', name: 'app_nouveau_menu')]
    public function nouveauMenu(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        $menu = new Menu();

        // Préparation du formulaire et de la requête
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $menu->setCreatedAt(new \DateTime());

            $em->persist($menu);
            $em->flush();

            return $this->redirectToRoute('app_employe_gestion_menu');
        };

        // createView() convertit le formulaire en version affichable pour Twig
        return $this->render('employe/nouveau-menu.html.twig', [
            'form' => $form->createView(),
            'menu' => $menu
        ]);
    }

    // Prévisualisation d'un menu
    #[Route('/employe/gestion-menu/previsualisation/{id}', name: 'app_previsualisation_menu')]
    public function detailMenu(Menu $menu): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        return $this->render('employe/previsualisation.html.twig', [
            'menu' => $menu,
        ]);
    }

    // Modification d'un menu
    #[Route('/employe/gestion-menu/modification/{id}', name: 'app_menu_modifier')]
    public function modifierMenu(Menu $menu, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_employe_gestion_menu');
        };

        return $this->render('employe/modification-menu.html.twig', [
            'form' => $form->createView(),
            'menu' => $menu
        ]);
    }

    // Désactiver un menu
    #[Route('/employe/gestion-menu/desactiver/{id}', name: 'app_menu_desactiver')]
    public function desactiveMenu(Menu $menu, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        // Empêche une double désactivation
        if ($menu->getDeletedAt() !== null) {
            return $this->redirectToRoute('app_employe_gestion_menu');
        };

        $menu->setDeletedAt(new \DateTime());
        $em->flush();

        return $this->redirectToRoute('app_employe_gestion_menu');
    }

    // Ajouter un plat et afficahge
    #[Route('/employe/gestion-menu/plat', name: 'app_menu_nouveau_plat')]
    public function nouveauPlat(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        PlatRepository $platRepo,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        $plats = $platRepo->findBy([
            'deletedAt' => null
        ]);

        $plat = new Plat();

        $form = $this->createForm(PlatType::class, $plat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $photo = $form->get('photo')->getData();

            if ($photo) {

                $nomOriginal = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);

                $safeNom = $slugger->slug($nomOriginal);

                $nouveauNom = $safeNom . '-' . uniqid() . '.' . $photo->guessExtension();

                $photo->move(
                    $this->getParameter('image_directory'),
                    $nouveauNom
                );

                $plat->setPhoto($nouveauNom);
            }

            $plat->setCreatedAt(new \DateTime());

            $em->persist($plat);
            $em->flush();

            return $this->redirectToRoute('app_menu_nouveau_plat');
        }

        return $this->render('employe/nouveau-plat.html.twig', [
            'form' => $form->createView(),
            'plat' => $plats,
        ]);
    }

    // Désactivation d'un plat
    #[Route('/employe/gestion-menu/plat/supprime/{id}', 'app_supprime_plat')]
    public function supprimerPlat(Plat $plat, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        $menusActifs = $plat->getMenus()->filter(function ($menu) {
            return $menu->getDeletedAt() === null;
        });

        // Condition pour la vérification d'une relation entre un plat et un menu
        if (!$menusActifs->isEmpty()) {
            $this->addFlash('error',  $plat->getNomPlat() . ' est impossible à supprimer, ce plat est utilisé dans un menu.');
            return $this->redirectToRoute('app_menu_nouveau_plat');
        };

        $photo = $plat->getPhoto();

        if ($photo) {
            $pathPhoto = $this->getParameter('image_directory') . '/' . $photo;
            if (file_exists($pathPhoto)) {
                // Supprime la photo du fichier uploads/plat
                unlink($pathPhoto);
            };
        };

        $plat->setDeletedAt(new \DateTime());
        $em->flush();

        $this->addFlash('success', $plat->getNomPlat() . ' a été supprimé');

        return $this->redirectToRoute('app_menu_nouveau_plat');
    }

    // Ajouter un thème et affichage
    #[Route('/employe/gestion-menu/theme', name: 'app_nouveau_theme')]
    public function nouveauTheme(
        ThemeRepository $themeRepo,
        EntityManagerInterface $em,
        Request $request
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        $themes = $themeRepo->findBy([
            'deletedAt' => null
        ]);

        $theme = new Theme();

        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $theme->setCreatedAt(new \DateTime());
            $em->persist($theme);
            $em->flush();

            return $this->redirectToRoute('app_nouveau_theme');
        };

        return $this->render('employe/nouveau-theme.html.twig', [
            'theme' => $themes,
            'form' => $form->createView(),
        ]);
    }

    // Supprimer un theme
    #[Route('/employe/gestion-menu/theme/supprime/{id}', name: 'app_supprime_theme')]
    public function supprimerTheme(Theme $theme, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        $menusActifs = $theme->getMenus()->filter(function ($menu) {
            return $menu->getDeletedAt() === null;
        });

        // Condition pour la vérification d'une relation entre un theme et un menu
        if (!$menusActifs->isEmpty()) {
            $this->addFlash('error',  $theme->getDescription() . ' est impossible à supprimer, ce thème est utilisé dans un menu.');
            return $this->redirectToRoute('app_nouveau_theme');
        }

        $theme->setDeletedAt(new \DateTime());
        $em->flush();

        $this->addFlash('success', $theme->getDescription() . ' a été supprimé');

        return $this->redirectToRoute('app_nouveau_theme');
    }

    // Ajouter un régime
    #[Route('/employe/gestion-menu/regime', name: 'app_nouveau_regime')]
    public function nouveauRegime(RegimeRepository $regimeRepo, EntityManagerInterface $em, Request $request): response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        $regimes = $regimeRepo->findBy([
            'deletedAt' => null
        ]);

        $regime = new Regime();

        $form = $this->createForm(RegimeType::class, $regime);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $regime->setCreatedAt(new \DateTime());
            $em->persist($regime);
            $em->flush();

            return $this->redirectToRoute('app_nouveau_regime');
        };

        return $this->render('employe/nouveau-regime.html.twig', [
            'regime' => $regimes,
            'form' => $form->createView(),
        ]);
    }

    // Suppression d'un régime
    #[Route('/employe/gestion-menu/regime/supprimer/{id}', name: 'app_supprime_regime')]
    public function supprimerRegime(Regime $regime, EntityManagerInterface $em): response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        $menusActifs = $regime->getMenus()->filter(function ($menu) {
            return $menu->getDeletedAt() === null;
        });

        // Condition pour la vérification d'une relation entre un régime et un menu
        if (!$menusActifs->isEmpty()) {
            $this->addFlash('error',  $regime->getDescription() . ' est impossible à supprimer, ce régime est utilisé dans un menu.');
            return $this->redirectToRoute('app_nouveau_regime');
        }

        $regime->setDeletedAt(new \DateTime());
        $em->flush();

        $this->addFlash('success', $regime->getDescription() . ' a été supprimé');

        return $this->redirectToRoute('app_nouveau_regime');
    }

    // Horaires
    #[Route('/employe/horaire', name: 'app_employe_horaire')]
    public function horaire(HoraireRepository $horaire): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        return $this->render('employe/horaire.html.twig', [
            'horaires' => $horaire->findAll(),
        ]);
    }

    // Modification des horaires
    #[Route('/employe/horaire/modifier/{id}', name: 'app_employe_nouveau_horaire')]
    public function modifierHoraire(
        Horaire $horaire,
        Request $request,
        EntityManagerInterface $em
    ): response {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        $form = $this->createForm(HoraireType::class, $horaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('app_employe_horaire');
        };

        return $this->render('employe/horaire-form.html.twig', [
            'form' => $form->createView(),
            'horaire' => $horaire,
        ]);
    }
}
