<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Annonce;
use App\Entity\Compte;
use Symfony\Component\HttpFoundation\Request;
use DateTime;

class CompteController extends AbstractController
{
    #[Route('/compte', name: 'app_compte')]
    public function index(): Response
    {
        return $this->render('compte/index.html.twig', [
            'controller_name' => 'CompteController',
        ]);
    }

    public function consulterCompte(ManagerRegistry $doctrine, int $id){
        $compte = $doctrine->getRepository(Compte::class)->find($id);

        if(!$compte){
            throw $this->createNotFoundException(
                'Aucun compte trouvé avec comme identifiant '.$id
            );
        }
        return $this->render('compte/profil.html.twig', [
        'compte'=>$compte,]);
    }

    public function listerMesAnnonce(Request $request,ManagerRegistry $doctrine, int $idCompte){
        $compte = $doctrine->getRepository(Compte::class)->find($idCompte);

        $repository = $doctrine->getRepository(Annonce::class);
    
        $annonces = $repository->findBy(['compte' => $compte]);

    
        return $this->render('compte/mesAnnonces.html.twig', [
            'pAnnonces' => $annonces,
            'compte' => $compte,
        ]);
    }
}
