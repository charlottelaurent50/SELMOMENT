<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Domaine;
use App\Entity\Categorie; // Ajouter ces lignes pour les autres entités
use App\Entity\Type;
use App\Entity\Statut;

use App\Form\DomaineType;
use App\Form\CategorieType; // Ajouter ces lignes pour les autres formulaires
use App\Form\TypeType;
use App\Form\StatutType;


use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DomaineController extends AbstractController
{
    #[Route('/domaine', name: 'app_domaine')]
    public function index(): Response
    {
        return $this->render('domaine/index.html.twig', [
            'controller_name' => 'DomaineController',
        ]);
    }

    public function ajouterDomaine(
        Request $request,
        ManagerRegistry $doctrine
    ): Response {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->forward('App\Controller\ErrorController::showAccessDenied', [
                'exception' => new AccessDeniedException("Accès refusé ! Vous devez être administrateur pour accéder à cette page.")
            ]);  
        }
        $domaine = new Domaine();
        $categorie = new Categorie(); // Ajouter ces lignes pour les autres entités
        $type = new Type();
        $statut = new Statut();

        $domaineForm = $this->createForm(DomaineType::class, $domaine);
        $categorieForm = $this->createForm(CategorieType::class, $categorie); // Ajouter ces lignes pour les autres formulaires
        $typeForm = $this->createForm(TypeType::class, $type);
        $statutForm = $this->createForm(StatutType::class, $statut);

        $domaineForm->handleRequest($request);
        $categorieForm->handleRequest($request);
        $typeForm->handleRequest($request);
        $statutForm->handleRequest($request);

        $entityManager = $doctrine->getManager();

        if ($domaineForm->isSubmitted() && $domaineForm->isValid()) {
            $entityManager->persist($domaine);
            $entityManager->flush();
        }

        if ($categorieForm->isSubmitted() && $categorieForm->isValid()) {
            $entityManager->persist($categorie);
            $entityManager->flush();
        }

        if ($typeForm->isSubmitted() && $typeForm->isValid()) {
            $entityManager->persist($type);
            $entityManager->flush();
        }

        if ($statutForm->isSubmitted() && $statutForm->isValid()) {
            $entityManager->persist($statut);
            $entityManager->flush();
        }

        $repository = $doctrine->getRepository(Domaine::class);
        $lesDomaines = $repository->findAll();

        $repository = $doctrine->getRepository(Categorie::class);
        $lesCategories = $repository->findAll();

        $repository = $doctrine->getRepository(Type::class);
        $lesTypes = $repository->findAll();

        $repository = $doctrine->getRepository(Statut::class);
        $lesStatuts = $repository->findAll();

        return $this->render('admin/domaine/ajouter.html.twig', [
            'domaineForm' => $domaineForm->createView(),
            'categorieForm' => $categorieForm->createView(),
            'typeForm' => $typeForm->createView(),
            'statutForm' => $statutForm->createView(),
            'domaines' => $lesDomaines,
            'categories' => $lesCategories,
            'types' => $lesTypes,
            'statuts' => $lesStatuts,
        ]);
    }

    public function supprimerDomaine(
        int $id,
        ManagerRegistry $doctrine
    ): Response {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->forward('App\Controller\ErrorController::showAccessDenied', [
                'exception' => new AccessDeniedException("Accès refusé ! Vous devez être administrateur pour accéder à cette page.")
            ]);  
        }
        $entityManager = $doctrine->getManager();
        $domaine = $entityManager->getRepository(Domaine::class)->find($id);

        if (!$domaine) {
            throw $this->createNotFoundException('Domaine non trouvé pour l\'id '.$id);
        }

        $entityManager->remove($domaine);
        $entityManager->flush();

        return $this->redirectToRoute('domaineAjouter');
    }

    public function supprimerCategorie(
        int $id,
        ManagerRegistry $doctrine
    ): Response {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->forward('App\Controller\ErrorController::showAccessDenied', [
                'exception' => new AccessDeniedException("Accès refusé ! Vous devez être administrateur pour accéder à cette page.")
            ]);  
        }
        $entityManager = $doctrine->getManager();
        $categorie = $entityManager->getRepository(Categorie::class)->find($id);

        if (!$categorie) {
            throw $this->createNotFoundException('Catégorie non trouvé pour l\'id '.$id);
        }

        $entityManager->remove($categorie);
        $entityManager->flush();

        return $this->redirectToRoute('domaineAjouter');
        }

    public function supprimerType(
        int $id,
        ManagerRegistry $doctrine
    ): Response {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->forward('App\Controller\ErrorController::showAccessDenied', [
                'exception' => new AccessDeniedException("Accès refusé ! Vous devez être administrateur pour accéder à cette page.")
            ]);  
        }
        $entityManager = $doctrine->getManager();
        $type = $entityManager->getRepository(Type::class)->find($id);

        if (!$type) {
            throw $this->createNotFoundException('Type non trouvé pour l\'id '.$id);
        }

        $entityManager->remove($type);
        $entityManager->flush();
        return $this->redirectToRoute('domaineAjouter');
        }

    public function supprimerStatut(
        int $id,
        ManagerRegistry $doctrine
    ): Response {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->forward('App\Controller\ErrorController::showAccessDenied', [
                'exception' => new AccessDeniedException("Accès refusé ! Vous devez être administrateur pour accéder à cette page.")
            ]);  
        }
        $entityManager = $doctrine->getManager();
        $statut = $entityManager->getRepository(Statut::class)->find($id);

        if (!$statut) {
            throw $this->createNotFoundException('Statut non trouvé pour l\'id '.$id);
        }

        $entityManager->remove($statut);
        $entityManager->flush();
        return $this->redirectToRoute('domaineAjouter');
        }

}
