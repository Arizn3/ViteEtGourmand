<?php

namespace App\Controller;

use App\Entity\Avis;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CommandeRepository;
use App\Repository\AvisRepository;
use App\Repository\MenuRepository;
use App\Form\MenuType;
use App\Entity\Commande;
use App\Entity\Horaire;
use App\Entity\Menu;
use App\Entity\Plat;
use App\Entity\Regime;
use App\Entity\Theme;
use App\Entity\Utilisateur;
use App\Form\HoraireType;
use App\Form\PlatType;
use App\Form\RegimeType;
use App\Form\ThemeType;
use App\Repository\HoraireRepository;
use App\Repository\PlatRepository;
use App\Repository\RegimeRepository;
use App\Repository\ThemeRepository;
use App\Document\Stat;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

// Controller pour l'espace employé
final class EmployeController extends AbstractController
{
    // Affichage des commandes
    #[Route('/employe/commandes', name: 'app_employe_commande')]
    public function index(CommandeRepository $CommandeRepo, Request $request): Response
    {
        // Contrôle d'accès
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        // Variable en cas de filtre (commande active)
        $idFilter = $request->query->get('idFilter');
        $emailFilter = $request->query->get('email');
        $statutFilter = $request->query->get('statut');

        // Filtre et affichage des commandes actives
        $query = $CommandeRepo->createQueryBuilder('c')
            ->join('c.utilisateur', 'u');

        if ($idFilter) {
            $query->andWhere('c.id = :id')
                ->setParameter('id', $idFilter);
        };
        if ($emailFilter) {
            $query->andWhere('u.email LIKE :email')
                ->setParameter('email', '%' . $emailFilter . '%');
        };
        if ($statutFilter) {
            $query->andWhere('c.statut = :statut')
                ->setParameter('statut', $statutFilter);
        };

        // Récupération des commandes terminée ou annulée grâce au paramètre ->setParameter()
        $commandesTerminees = $CommandeRepo->createQueryBuilder('c')
            ->where('c.statut IN (:statuts)')
            ->setParameter('statuts', ['Terminer', 'Annuler']);

        // Variable récupérer en cas de filtre (commande terminer ou annuler)
        $idTerminer = $request->query->get('idTerminer');
        $emailTerminer = $request->query->get('emailTerminer');
        $statutTerminer = $request->query->get('statutTerminer');

        // Filtre pour les commandes Terminer ou Annuler
        $queryTerminer = $CommandeRepo->createQueryBuilder('c')
            ->join('c.utilisateur', 'u')
            ->where('c.statut IN (:statuts)')
            ->setParameter('statuts', ['Terminer', 'Annuler']);

        if ($idTerminer) {
            $queryTerminer->andWhere('c.id = :id')
                ->setParameter('id', $idTerminer);
        };
        if ($emailTerminer) {
            $queryTerminer->andWhere('u.email LIKE :email')
                ->setParameter('email', '%' . $emailTerminer . '%');
        };
        if ($statutTerminer) {
            $queryTerminer->andWhere('c.statut LIKE :statut')
                ->setParameter('statut', '%' . $statutTerminer . '%');
        };

        $commandes = $query->getQuery()->getResult();
        $commandesTerminees = $queryTerminer->getQuery()->getResult();

        // Nombre des commandes actives pour Twig
        $commandeActives = array_filter($commandes, function ($c) {
            return !in_array($c->getStatut(), ['Terminer', 'Annuler']);
        });

        return $this->render('employe/index.html.twig', [
            'commandes' => $commandes,
            'commandesTerminees' => $commandesTerminees,
            'commandesActives' => $commandeActives
        ]);
    }

    // Affichage de l'adresse de livraison d'une commande
    #[Route('/employe/adresse/{id}', name: 'app_adresse_livraison')]
    public function adresseLivraison(Commande $commande): Response
    {
        // Contôle d'accès
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        // Récupération de l'adresse de la livraison
        return $this->render('employe/adresse.html.twig', [
            'commande' => $commande
        ]);
    }

    // Affichage d'information sur le client d'une commande
    #[Route('/employe/utilisateur/{id}', name: 'app_utilisateur')]
    public function detailUtilisateur(Utilisateur $utilisateur): Response
    {
        // Contôle d'accès
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        // Récupération des informations sur le client
        return $this->render('employe/utilisateur.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    // Page de changement du statut d'une commande
    #[Route('/employe/statut/{id}', name: 'app_commande_statut')]
    public function changementStatut(
        Commande $commande,
        Request $request,
        EntityManagerInterface $em,
        MailerInterface $mailer,
        DocumentManager $dm,
    ): Response {
        // Contôle d'accès
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        // Récupération du statut choisi par l'employé
        // La base de données est ensuite mise à jour
        if ($request->isMethod('POST')) {
            $statut = $request->request->get('statut');

            // Sécurité des statuts possible
            $statutPossible = [
                'Votre commande a été prise en compte',
                'Votre commande est en préparation',
                'En cours de livraison',
                'Commande livrée',
                'En attente du retour de matériel',
                'Terminer',
                'Annuler'
            ];
            if (!in_array($statut, $statutPossible)) {
                throw new \Exception('Statut Invalide');
            };

            // Système de mail en cas d'annulation d'une commande
            if ($statut === 'Annuler') {

                // Récupération du message de l'input (Twig)
                $messageEmail = $request->request->get('message_email');

                if (empty($messageEmail)) {
                    throw new BadRequestHttpException('Motif d\'annulation obligatoire');
                } else {
                    $email = (new Email())
                        ->from('Vite & Gourmand <33vitegourmand@gmail.com>')
                        ->to($commande->getUtilisateur()->getEmail())
                        ->subject('Annulation de votre commmande')
                        ->text('Bonjour,
                    
Votre commande a été annulée pour les raisons suivantes :
                    
' . $messageEmail . '
                    
Nous restons à votre disposition, cordialement.
L\'équipe Vite & Gourmand
                    ');

                    $mailer->send($email);
                };
            };

            // Enregistrement des données d'une commande dans MongoDB
            // avec un système de mail pour l'utilisateur
            if ($statut === 'Terminer') {

                // Information sur la commande terminée
                $menuNom = $commande->getMenu()->getTitre();
                $prix = $commande->getPrixMenu();
                $periode = (new \DateTime())->format('Y-m');

                // Chercher si le docmuent menu existe déjà
                // ainsi que sa période
                $stat = $dm->getRepository(Stat::class)->findOneBy([
                    'menu' => $menuNom,
                    'periode' => $periode
                ]);

                // Sinon création d'un nouveau document
                if (!$stat) {
                    $stat = new Stat();
                    $stat->setMenu($menuNom);
                    $stat->setPeriode($periode);
                    $stat->setTotalCommandes(0);
                    $stat->setChiffreAffaire(0);
                };

                // Incrémentation
                $stat->setTotalCommandes($stat->getTotalCommandes() + 1);
                $stat->setChiffreAffaire($stat->getChiffreAffaire() + $prix);

                $dm->persist($stat);
                $dm->flush();

                // Envoie de l'email de fin de commande a l'utilisateur
                $url = $this->generateUrl('app_login', [], UrlGeneratorInterface::ABSOLUTE_URL);
                $email = (new Email())
                    ->from('Vite & Gourmand <33vitegourmand@gmail.com>')
                    ->to($commande->getUtilisateur()->getEmail())
                    ->subject('Commmande terminée')
                    ->html("
                    <p>Bonjour,</p>
                    
                    <p>Votre commande est à présent terminée, nous espérons que vous vous êtes régalé.</p>

                    <p>Vous pouvez dès à présent vous rendre dans votre <a href='$url'>espace personnel</a> pour nous laisser un avis.</p>
                    
                    <p>Nous restons à votre disposition, cordialement.</p>
                    <p>L\'équipe Vite & Gourmand</p>
                    ");

                $mailer->send($email);
            };

            // Modification et persistance de la donnée
            $commande->setStatut($statut);
            $em->flush();

            return $this->redirectToRoute('app_employe_commande', $request->query->all());
        };

        return $this->render('employe/statut.html.twig', [
            'commande' => $commande
        ]);
    }

    // Liste des avis utilisateurs
    #[Route('/employe/avis', name: 'app_employe_avis')]
    public function avisUtilisateur(AvisRepository $avisRepo): Response
    {
        // Contôle d'accès
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        $avis = $avisRepo->findBy(['statut' => 'EN_ATTENTE']);

        return $this->render('employe/avis.html.twig', [
            'avis' => $avis,
        ]);
    }

    ## Les deux fonctions suivantes sépare la validation et l'annulation des avis, pour les utiliser,
    ## il faut d'abord enlever le paramètre dynamique dans le fichier Twig au niveau des liens 'action: '***''
    ## et changer l'URL du lien en 'app_avis_action' pour ensuite commenter la fonction actionAvis.

    # // Function de validation d'un avis
    # #[Route('/employe/avis/valider/{id}', name: 'app_avis_valider')]
    # public function validerAvis(Avis $avis, EntityManagerInterface $em): Response
    # {
    #     // Contrôle d'accès
    #     $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

    #     // Appel du Setter de Avis
    #     $avis->setStatut('REFUSER');
    #     $em->flush();

    #     return $this->redirectToRoute('app_employe_avis');
    # }

    # // Function d'annuation d'un avis
    # #[Route('/employe/avis/refuser/{id}', name: 'app_avis_refuser')]
    # public function refuserAvis(Avis $avis, EntityManagerInterface $em): Response
    # {
    #     // Contrôle d'accès
    #     $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

    #     // Appel du Setter de Avis
    #     $avis->setStatut('VALIDE');
    #     $em->flush();

    #     return $this->redirectToRoute('app_employe_avis');
    # }

    // Function gestion des avis
    #[Route('/employe/avis/{id}/{action}', name: 'app_avis_action')]
    public function actionAvis(Avis $avis, string $action, EntityManagerInterface $em): Response
    {
        // Contrôle d'accès
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        if ($action === 'VALIDE') {
            $avis->setStatut('VALIDE');
        } elseif ($action === 'REFUSER') {
            $avis->setStatut('REFUSER');
        } else {
            throw $this->createNotFoundException();
        };

        // Persistance des donnnées en base
        $em->flush();

        return $this->redirectToRoute('app_employe_avis');
    }

    // Historique des avis
    #[Route('/employe/avis/historique', name: 'app_historique_avis')]
    public function avisHistorique(AvisRepository $avisRepo): Response
    {
        // Contrôle d'accès
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        // On récupère tous les avis validés ou refuser
        $avis = $avisRepo->findBy(['statut' => ['VALIDE', 'REFUSER']]);

        return $this->render('employe/historique.html.twig', [
            'avis' => $avis,
        ]);
    }

    // Liste des menus actifs
    #[Route('/employe/gestion-menu', name: 'app_employe_gestion_menu')]
    public function gestionMenu(MenuRepository $menuRepo): Response
    {
        // Contrôle d'accès
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        // Commande encore actif récupérer
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
        // Contrôle d'accès
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        $menu = new Menu();

        // Préparation du formulaire et de la requête
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $menu->setCreatedAt(new \DateTime());

            // Persistance des données
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
        // Contrôle d'accès
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        return $this->render('employe/previsualisation.html.twig', [
            'menu' => $menu,
        ]);
    }

    // Modification d'un menu
    #[Route('/employe/gestion-menu/modification/{id}', name: 'app_menu_modifier')]
    public function modifierMenu(Menu $menu, Request $request, EntityManagerInterface $em): Response
    {
        // Contrôle d'accès
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        // Préparation du formulaire et de la requête
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
        // Contrôle d'accès
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        // Empêche une double désactivation
        if ($menu->getDeletedAt() !== null) {
            return $this->redirectToRoute('app_employe_gestion_menu');
        };

        // Désactivation du menu
        $menu->setDeletedAt(new \DateTime());
        $em->flush();

        return $this->redirectToRoute('app_employe_gestion_menu');
    }

    // Ajouter un plat
    #[Route('/employe/gestion-menu/plat', name: 'app_menu_nouveau_plat')]
    public function nouveauPlat(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        PlatRepository $platRepo,
    ): Response {
        // Contrôle d'accès
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        $plats = $platRepo->findAll();

        // Nouvelle instance de l'Entité Plat
        $plat = new Plat();

        // Création d'un nouveau formulaire et d'une requête
        $form = $this->createForm(PlatType::class, $plat);
        $form->handleRequest($request);

        // Photo
        $photo = $form->get('photo')->getData();

        // Mapping de la photo sous condition
        if ($photo) {

            $nomOriginal = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
            // Sécuriser le nom de la photo.
            $safeNom = $slugger->slug($nomOriginal);

            $nouveauNom = $safeNom . '-' . uniqid() . '.' . $photo->guessExtension();

            $photo->move(
                $this->getParameter('image_directory'),
                $nouveauNom
            );

            $plat->setPhoto($nouveauNom);
        }

        if ($form->isSubmitted() && $form->isValid()) {

            // Persistance des données en base
            $em->persist($plat);
            $em->flush();

            return $this->redirectToRoute('app_menu_nouveau_plat');
        };

        return $this->render('employe/nouveau-plat.html.twig', [
            'form' => $form->createView(),
            'plat' => $plats,
        ]);
    }

    // Suppression d'un plat
    #[Route('/employe/gestion-menu/plat/supprime/{id}', 'app_supprime_plat')]
    public function supprimerPlat(Plat $plat, EntityManagerInterface $em): Response
    {
        // Contrôle d'accès
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        // Vérification relation
        if (!$plat->getMenus()->isEmpty()) {
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

        $em->remove($plat);
        $em->flush();

        $this->addFlash('success', $plat->getNomPlat() . ' a été supprimé');

        return $this->redirectToRoute('app_menu_nouveau_plat');
    }

    // Ajouter un thème
    #[Route('/employe/gestion-menu/theme', name: 'app_nouveau_theme')]
    public function nouveauTheme(
        ThemeRepository $themeRepo,
        EntityManagerInterface $em,
        Request $request
    ): Response {
        // Contrôle d'accès
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        $theme = new Theme();

        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($theme);
            $em->flush();

            return $this->redirectToRoute('app_nouveau_theme');
        };

        return $this->render('employe/nouveau-theme.html.twig', [
            'theme' => $themeRepo->findAll(),
            'form' => $form->createView(),
        ]);
    }

    // Supprimer un theme
    #[Route('/employe/gestion-menu/theme/supprime/{id}', name: 'app_supprime_theme')]
    public function supprimerTheme(Theme $theme, EntityManagerInterface $em): Response
    {
        // Contrôle d'accès
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        if (!$theme->getMenus()->isEmpty()) {
            $this->addFlash('error',  $theme->getDescription() . ' est impossible à supprimer, ce thème est utilisé dans un menu.');
            return $this->redirectToRoute('app_nouveau_theme');
        }

        $em->remove($theme);
        $em->flush();

        $this->addFlash('success', $theme->getDescription() . ' a été supprimé');

        return $this->redirectToRoute('app_nouveau_theme');
    }

    // Ajouter un régime
    #[Route('/employe/gestion-menu/regime', name: 'app_nouveau_regime')]
    public function nouveauRegime(RegimeRepository $regimeRepo, EntityManagerInterface $em, Request $request): response
    {
        // Contrôle d'accès
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        $regime = new Regime();

        $form = $this->createForm(RegimeType::class, $regime);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($regime);
            $em->flush();

            return $this->redirectToRoute('app_nouveau_regime');
        };

        return $this->render('employe/nouveau-regime.html.twig', [
            'regime' => $regimeRepo->findAll(),
            'form' => $form->createView(),
        ]);
    }

    // Suppression d'un régime
    #[Route('/employe/gestion-menu/regime/supprimer/{id}', name: 'app_supprime_regime')]
    public function supprimerRegime(Regime $regime, EntityManagerInterface $em): response
    {
        // Contrôle d'accès
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        if (!$regime->getMenus()->isEmpty()) {
            $this->addFlash('error',  $regime->getDescription() . ' est impossible à supprimer, ce régime est utilisé dans un menu.');
            return $this->redirectToRoute('app_nouveau_regime');
        }

        $em->remove($regime);
        $em->flush();

        $this->addFlash('success', $regime->getDescription() . ' a été supprimé');

        return $this->redirectToRoute('app_nouveau_regime');
    }

    // Horaires
    #[Route('/employe/horaire', name: 'app_employe_horaire')]
    public function horaire(HoraireRepository $horaire): Response
    {
        // Contrôle d'accès
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
        // Contrôle d'accès
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
