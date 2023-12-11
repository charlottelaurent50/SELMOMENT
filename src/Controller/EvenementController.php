<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Evenement;
use App\Entity\Image;
use App\Form\EvenementType;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Naming\SlugNamer;
use Vich\UploaderBundle\Util\Transliterator;
use Cocur\Slugify\Slugify;


use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Symfony\Component\Filesystem\Filesystem;

use Symfony\Component\HttpFoundation\Request;

class EvenementController extends AbstractController
{
    #[Route('/evenement', name: 'app_evenement')]
    public function index(): Response
    {
        return $this->render('evenement/index.html.twig', [
            'controller_name' => 'EvenementController',
        ]);
    }

    public function ajouterEvenement(Request $request, ManagerRegistry $doctrine)
    {
        $evenement = new Evenement();
    
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        $entityManager = $doctrine->getManager();

    
        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les images transmises
            $images = $form->get('images')->getData();
    
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
                $evenement->addImage($img);
                $entityManager->persist($img);
            }
            $archive=0;
            
    
            $entityManager->persist($evenement);
            $entityManager->flush();

            $repository = $doctrine->getRepository(Evenement::class);
            $evenements = $repository->findBy(
                [],
                ['date_ev' => 'DESC']
            );
    
            return $this->render('evenement/lister.html.twig', [
                'pEvenements' => $evenements,]);	
        }
    
        return $this->render('admin/evenement/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function listerEvenement(ManagerRegistry $doctrine){
        $repository = $doctrine->getRepository(Evenement::class);
            $evenement = $repository->findBy(
                [],
                ['date_ev' => 'DESC']
            );
        return $this->render('evenement/lister.html.twig', [
            'pEvenements' => $evenement,]);	
            
    }

    public function supprimerEvenement(int $id, ManagerRegistry $doctrine, Filesystem $filesystem): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->forward('App\Controller\ErrorController::showAccessDenied', [
                'exception' => new AccessDeniedException("Accès refusé ! Vous devez être administrateur pour accéder à cette page.")
            ]);  
        }
        $entityManager = $doctrine->getManager();
        $repository = $doctrine->getRepository(Evenement::class);

        $evenement = $repository->find($id);

        if (!$evenement) {
            throw $this->createNotFoundException('Aucun événement trouvé pour l\'id '.$id);
        }

        // Suppression de l'image dans le dossier uploads
        foreach ($evenement->getImages() as $image) {
            $cheminImage = $this->getParameter('images_directory').'/'.$image->getChemin();
            if ($filesystem->exists($cheminImage)) {
                $filesystem->remove($cheminImage);
            }
        }

        // Suppression de l'événement et de ses images dans la base de données
        $entityManager->remove($evenement);
        $entityManager->flush();

        // Rediriger vers la liste des événements après la suppression
        $evenements = $repository->findBy([], ['date_ev' => 'DESC']);
        return $this->render('evenement/lister.html.twig', ['pEvenements' => $evenements]);
    }
}
