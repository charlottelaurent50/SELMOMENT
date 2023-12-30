<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Annonce;
use App\Entity\Compte;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

use App\Form\CompteModifierAdminType;


#[Security('is_granted("ROLE_ADMIN")')]
class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->forward('App\Controller\ErrorController::showAccessDenied', [
                'exception' => new AccessDeniedException("Accès refusé ! Vous devez être administrateur pour accéder à cette page.")
            ]);        
        }
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    public function listerCompte(ManagerRegistry $doctrine){
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->forward('App\Controller\ErrorController::showAccessDenied', [
                'exception' => new AccessDeniedException("Accès refusé ! Vous devez être administrateur pour accéder à cette page.")
            ]);  
        }
        $repository = $doctrine->getRepository(Compte::class);
        $compte = $repository->findBy(
           [],
           ['num_adherent' => 'ASC']
       );
        return $this->render('admin/compte/lister.html.twig', [
            'pCompte' => $compte,]);	
            
    }
    public function listerToutesLesAnnonces(ManagerRegistry $doctrine){
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->forward('App\Controller\ErrorController::showAccessDenied', [
                'exception' => new AccessDeniedException("Accès refusé ! Vous devez être administrateur pour accéder à cette page.")
            ]);  
        }

        $repository = $doctrine->getRepository(Annonce::class);
    
        $annonces = $repository->findAll();

        return $this->render('admin/annonce/lister.html.twig', [
            'pAnnonces' => $annonces,
        ]);
    }
    
    public function modifierCompteAdmin(ManagerRegistry $doctrine, $id, Request $request){
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->forward('App\Controller\ErrorController::showAccessDenied', [
                'exception' => new AccessDeniedException("Accès refusé ! Vous devez être administrateur pour accéder à cette page.")
            ]);  
        }
 
        $compte = $doctrine->getRepository(Compte::class)->find($id);
     
        if (!$compte) {
            
            throw $this->createNotFoundException('Aucun compte trouvé avec le numéro '.$id);
        }
        else
        {
                $form = $this->createForm(CompteModifierAdminType::class, $compte);
                $form->handleRequest($request);
     
                if ($form->isSubmitted() && $form->isValid()) {
     
                     $compte = $form->getData();
                     if ($form->get('actif')->getData() === true) {
                        $compte->setActif(true);
                        $compte->setRoles(['ROLE_USERACTIF']);
                    } else {
                        $compte->setActif(false);
                        $compte->setRoles(['ROLE_USER']);
                    }
                    if ($form->get('archive')->getData() === true) {
                        $compte->setArchive(true);
                    } else {
                        $compte->setArchive(false);
                    }
                     $entityManager = $doctrine->getManager();
                     $entityManager->persist($compte);
                     $entityManager->flush();
                     return $this->redirectToRoute('compteLister');

               }
               else{
                    return $this->render('admin/compte/modifier.html.twig', array('form' => $form->createView(),));
               }
            }
     }

     #[Route('/admin/create_admin', name: 'create_admin')]
    public function createAdmin(ManagerRegistry $doctrine, UserPasswordHasherInterface $userPasswordHasher)
        {
            // Créer un nouvel utilisateur (compte)
            $admin = new Compte();
    
            // Définir les propriétés pour le compte admin
            $admin->setNumAdherent(2023000);
            $admin->setNom('Admin');
            $admin->setPrenom('Admin');
            $admin->setAdresse('0');
            $admin->setCodePostal('50800');
            $admin->setVille('Villedieu-les-poêles');
            $admin->setTelephone('0000000000');
            $admin->setEmail('selmomentvilledieu@gmail.com');
            $admin->setActif(true);
            $admin->setArchive(false);
            
            // Générer un mot de passe pour le compte admin
            $password = 'selm0ment@dmin'; // Remplacez cette valeur par le mot de passe souhaité
            
            $admin->setPassword(
                $userPasswordHasher->hashPassword(
                    $admin,
                    $password)
            );
    
            // Ajouter le rôle "ROLE_ADMIN"
            $admin->setRoles(['ROLE_ADMIN']);
    
            // Enregistrer le nouvel utilisateur dans la base de données
            $entityManager = $doctrine->getManager();
            $entityManager->persist($admin);
            $entityManager->flush();
    
            return $this->redirectToRoute('admin'); // Redirection vers une page appropriée après la création de l'administrateur.
        }
     
    
}
