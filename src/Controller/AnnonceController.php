<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Annonce;
use App\Entity\Image;
use App\Form\AnnonceType;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Naming\SlugNamer;
use Vich\UploaderBundle\Util\Transliterator;
use Cocur\Slugify\Slugify;


use Symfony\Component\HttpFoundation\Request;

class AnnonceController extends AbstractController
{
    #[Route('/annonce', name: 'app_annonce')]
    public function index(): Response
    {
        return $this->render('annonce/index.html.twig', [
            'controller_name' => 'AnnonceController',
        ]);
    }

    public function listerAnnonce(ManagerRegistry $doctrine){
        $repository = $doctrine->getRepository(Annonce::class);
        $annonce = $repository->findAll();
        return $this->render('annonce/lister.html.twig', [
            'pAnnonces' => $annonce,]);	
            
    }

    public function consulterAnnonce(ManagerRegistry $doctrine, int $id){
        $annonce = $doctrine->getRepository(Annonce::class)->find($id);

        if(!$annonce){
            throw $this->createNotFoundException(
                'Aucun annonce trouvé avec comme identifiant '.$id
            );
        }
        
        return $this->render('annonce/consulter.html.twig', [
        'annonce'=>$annonce,]);
    }

    public function ajouterAnnonce(Request $request, ManagerRegistry $doctrine)
    {
        $annonce = new Annonce();
    
        $form = $this->createForm(AnnonceType::class, $annonce);
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
                $annonce->addImage($img);
                $entityManager->persist($img);
            }

            $annonce->setDatePublication(new \DateTime());
            
    
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
}
