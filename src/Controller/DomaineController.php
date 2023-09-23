<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Domaine;
use App\Form\DomaineType;

class DomaineController extends AbstractController
{
    #[Route('/domaine', name: 'app_domaine')]
    public function index(): Response
    {
        return $this->render('domaine/index.html.twig', [
            'controller_name' => 'DomaineController',
        ]);
    }

    public function ajouterDomaine(Request $request,ManagerRegistry $doctrine){
        $domaine = new Domaine();
        $form = $this->createForm(DomaineType::class, $domaine);
        $form->handleRequest($request);

        $repository = $doctrine->getRepository(Domaine::class);
        $lesDomaines = $repository->findAll( );
     
        if ($form->isSubmitted() && $form->isValid()) {
     
                $domaine = $form->getData();
     
                $entityManager = $doctrine->getManager();
                $entityManager->persist($domaine);
                $entityManager->flush();
     
            return $this->render('admin/domaine/ajouter.html.twig', 
            array(
                'form' => $form->createView(),
                'domaines' => $lesDomaines));
        }
        else
            {
                return $this->render('admin/domaine/ajouter.html.twig', 
                array(
                    'form' => $form->createView(),
                    'domaines' => $lesDomaines));
        }
    }
}
