<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Annonce;
use App\Entity\Image;
use App\Form\AnnonceType;
use App\Form\AnnonceModifierType;
use App\Entity\Type;
use App\Entity\Compte;
use App\Entity\Domaine;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Naming\SlugNamer;
use Vich\UploaderBundle\Util\Transliterator;
use Cocur\Slugify\Slugify;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


use Symfony\Component\Filesystem\Filesystem;

class AnnonceController extends AbstractController
{
    #[Route('/annonce', name: 'app_annonce')]
    public function index(): Response
    {
        return $this->render('annonce/index.html.twig', [
            'controller_name' => 'AnnonceController',
        ]);
    }

    public function consulterAnnonce(ManagerRegistry $doctrine, int $id)
    {
        if (!$this->isGranted('ROLE_USERACTIF')) {
            return $this->forward('App\Controller\ErrorController::showAccessDenied', [
                'exception' => new AccessDeniedException("Accès temporairement refusé. Veuillez patienter, votre profil est en attente de confirmation de la part de l’administrateur.")
            ]);
        }
        $annonce = $doctrine->getRepository(Annonce::class)->find($id);

        if (!$annonce) {
            return $this->forward('App\Controller\ErrorController::showAccessDenied', [
                'exception' => new AccessDeniedException("Pas d'annonce avec cet identifiant")
            ]);;
        }

        return $this->render('annonce/consulter.html.twig', [
            'annonce' => $annonce,
        ]);
    }

    public function listerAnnonce(Request $request, ManagerRegistry $doctrine)
    {
        if (!$this->isGranted('ROLE_USERACTIF')) {
            return $this->forward('App\Controller\ErrorController::showAccessDenied', [
                'exception' => new AccessDeniedException("Accès temporairement refusé. Veuillez patienter, votre profil est en attente de confirmation de la part de l’administrateur.")
            ]);
        }
        $filtres = ['type' => '', 'domaine' => ''];

        $typeRepository = $doctrine->getRepository(Type::class);
        $types = $typeRepository->findAll();
        $domaineRepository = $doctrine->getRepository(Domaine::class);
        $domaines = $domaineRepository->findAll();

        $annonceRepository = $doctrine->getRepository(Annonce::class);
        $queryBuilder = $annonceRepository->createQueryBuilder('a')
            ->orderBy('a.date_publication', 'DESC'); // Tri par ordre décroissant de date de publication

        // Filtrer par type si le type est sélectionné dans la requête
        $type = $request->query->get('type');
        if ($type) {
            $queryBuilder->andWhere('a.type = :type')
            ->setParameter('type', $type);
            $filtres['type'] = $type;
        }

        $domaine = $request->query->get('domaine');
        if ($domaine) {
            $queryBuilder->andWhere('a.domaine = :domaine')
            ->setParameter('domaine', $domaine);
            $filtres['domaine'] = $domaine;
        }

        $annonces = $queryBuilder->getQuery()->getResult();

        // Rendre un rendu partiel pour mettre à jour la liste des annonces sans rafraîchir la page entière
        return $this->render('annonce/lister.html.twig', [
            'pAnnonces' => $annonces,
            'pTypes' => $types,
            'pDomaines' => $domaines,
            'filtres' => $filtres,
        ]);
    }



    public function ajouterAnnonce(Request $request, ManagerRegistry $doctrine)
    {
        if (!$this->isGranted('ROLE_USERACTIF')) {
            return $this->forward('App\Controller\ErrorController::showAccessDenied', [
                'exception' => new AccessDeniedException("Accès temporairement refusé. Veuillez patienter, votre profil est en attente de confirmation de la part de l’administrateur.")
            ]);
        }
        $annonce = new Annonce();

        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        $entityManager = $doctrine->getManager();


        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les images transmises
            $images = $form->get('images')->getData();
            $compteConnecte = $this->getUser();

            // On boucle sur les images
            foreach ($images as $image) {
                // On génère un nouveau nom de fichier
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();

                // On copie le fichier dans le dossier uploads
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );

                // On crée l'image dans la base de données
                $img = new Image();
                $img->setChemin($fichier);
                $annonce->addImage($img);
                $entityManager->persist($img);
            }
            $archive = 0;

            if ($form->get('pas_date_expiration')->getData() === true) {
                $annonce->setDateExpiration(null);
            } else {
                $annonce->setDateExpiration($form->get('date_expiration')->getData());
            }

            $annonce->setDatePublication(new \DateTime());
            $annonce->setArchive($archive);
            $annonce->setCompte($compteConnecte);


            $entityManager->persist($annonce);
            $entityManager->flush();

            return $this->render('annonce/consulter.html.twig', [
                'annonce' => $annonce,
            ]);
        }

        return $this->render('annonce/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    public function toggleArchive(Request $request, ManagerRegistry $doctrine, int $id)
    {
        $entityManager = $doctrine->getManager();
        $annonce = $entityManager->getRepository(Annonce::class)->find($id);

        if (!$annonce) {
            throw $this->createNotFoundException('Annonce non trouvée.');
        }

        if ($annonce->isArchive() == true) {
            $annonce->setArchive(false);
        } elseif ($annonce->isArchive() == false) {
            $annonce->setArchive(true);
        }
        $entityManager->flush();

        return $this->json([
            'code' => 200,
            'message' => 'annonce archivée'
        ], 200);
    }

    public function modifierAnnonce(Request $request, ManagerRegistry $doctrine, int $id)
    {


        $entityManager = $doctrine->getManager();
        $annonce = $entityManager->getRepository(Annonce::class)->find($id);
        $user = $this->getUser();
        if ($user !== $annonce->getCompte()) {
            return $this->forward('App\Controller\ErrorController::showAccessDenied', [
                'exception' => new AccessDeniedException("Accès refusé ! Ce n'est pas une de vos annonces")
            ]);
        }

        if (!$annonce) {
            throw $this->createNotFoundException('Annonce non trouvée.');
        }

        $form = $this->createForm(AnnonceModifierType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les images transmises
            $images = $form->get('images')->getData();
            $compteConnecte = $this->getUser();

            if ($images) {
                foreach ($annonce->getImages() as $image) {
                    $annonce->removeImage($image);
                }
                // On boucle sur les images
                foreach ($images as $image) {
                    // On génère un nouveau nom de fichier
                    $fichier = md5(uniqid()) . '.' . $image->guessExtension();

                    // On copie le fichier dans le dossier uploads
                    $image->move(
                        $this->getParameter('images_directory'),
                        $fichier
                    );

                    // On crée l'image dans la base de données
                    $img = new Image();
                    $img->setChemin($fichier);
                    $annonce->addImage($img);
                    $entityManager->persist($img);
                }
            }
            $archive = 0;

            if ($form->get('pas_date_expiration')->getData() === true) {
                $annonce->setDateExpiration(null);
            } else {
                $annonce->setDateExpiration($form->get('date_expiration')->getData());
            }

            $annonce->setDatePublication(new \DateTime());
            $annonce->setArchive($archive);
            $annonce->setCompte($compteConnecte);


            $entityManager->persist($annonce);
            $entityManager->flush();

            return $this->render('annonce/consulter.html.twig', [
                'annonce' => $annonce,
            ]);
        }

        return $this->render('annonce/modifier.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    public function supprimerAnnonce(int $id, ManagerRegistry $doctrine, Filesystem $filesystem): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->forward('App\Controller\ErrorController::showAccessDenied', [
                'exception' => new AccessDeniedException("Accès refusé ! Vous devez être administrateur pour accéder à cette page.")
            ]);
        }
        $entityManager = $doctrine->getManager();
        $repository = $doctrine->getRepository(Annonce::class);

        $annonce = $repository->find($id);

        if (!$annonce) {
            throw $this->createNotFoundException('Aucune annonce trouvée pour l\'id ' . $id);
        }

        // Suppression de l'image dans le dossier uploads
        foreach ($annonce->getImages() as $image) {
            $cheminImage = $this->getParameter('images_directory') . '/' . $image->getChemin();
            if ($filesystem->exists($cheminImage)) {
                $filesystem->remove($cheminImage);
            }
        }

        // Suppression de l'événement et de ses images dans la base de données
        $entityManager->remove($annonce);
        $entityManager->flush();

        return $this->redirectToRoute("annoncesAdmin");
    }
}
