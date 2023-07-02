<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Annonce;
use App\Entity\Image;
use App\Form\AnnonceType;
use App\Entity\Type;

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

    public function listerAnnonce(Request $request,ManagerRegistry $doctrine, int $idType){
        $typeId = $request->query->get('idType');

        $repository = $doctrine->getRepository(Annonce::class);
    
        if ($idType === 1) {
            $annonces = $repository->createQueryBuilder('a')
                ->leftJoin('a.type', 't')
                ->andWhere('t.id = :typeId')
                ->setParameter('typeId', 1)
                ->orderBy('a.date_publication', 'DESC') // Tri par ordre décroissant de date de publication
                ->getQuery()
                ->getResult();
        } elseif ($idType === 2) {
            $annonces = $repository->createQueryBuilder('a')
            ->leftJoin('a.type', 't')
            ->andWhere('t.id = :typeId')
            ->setParameter('typeId', 2)
            ->orderBy('a.date_publication', 'DESC') // Tri par ordre décroissant de date de publication
            ->getQuery()
            ->getResult();
        }
        else {
            $annonces = $repository->findAll();
        }
    
        return $this->render('annonce/lister.html.twig', [
            'pAnnonces' => $annonces,
        ]);
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
            $archive=0;

            $annonce->setDatePublication(new \DateTime());
            $annonce->setArchive($archive);
            
    
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
