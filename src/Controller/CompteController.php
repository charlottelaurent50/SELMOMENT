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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


use Symfony\Component\Security\Core\Exception\AccessDeniedException;


use App\Form\CompteModifierType;

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

    public function modifierCompte(ManagerRegistry $doctrine, $idCompte, Request $request, UserPasswordHasherInterface $userPasswordHasher){
        $user = $this->getUser();
        $compte = $doctrine->getRepository(Compte::class)->find($idCompte);
     
 
        if (!$compte) {
            throw $this->createNotFoundException('Aucun compte trouvé avec le numéro '.$idCompte);
        }
        elseif ($user !== $compte) {
            return $this->forward('App\Controller\ErrorController::showAccessDenied', [
                'exception' => new AccessDeniedException("Accès refusé ! Ce n'est pas votre compte")
            ]); 
        }
        else
        {
                $form = $this->createForm(CompteModifierType::class, $compte);
                $form->handleRequest($request);
                
     
                if ($form->isSubmitted() && $form->isValid()) {
                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user,
                            $form->get('password')->getData()
                        )
                    );
     
                     $compte = $form->getData();
                     
                     
                     $entityManager = $doctrine->getManager();
                     $entityManager->persist($compte);
                     $entityManager->flush();
                     
        return $this->render('compte/profil.html.twig', [
            'compte'=>$compte,]);

               }
               else{
                    return $this->render('compte/modifier.html.twig', array('form' => $form->createView(),));
               }
            }
     }
}
