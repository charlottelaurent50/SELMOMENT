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
    
            return $this->render('index/index.html.twig', [
            ]);
        }
    
        return $this->render('admin/evenement/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
