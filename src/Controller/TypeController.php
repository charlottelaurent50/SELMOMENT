<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Type;

class TypeController extends AbstractController
{
    #[Route('/type', name: 'app_type')]
    public function index(): Response
    {
        return $this->render('type/index.html.twig', [
            'controller_name' => 'TypeController',
        ]);
    }

    public function listerType(ManagerRegistry $doctrine){
        $repository = $doctrine->getRepository(Type::class);
        $type = $repository->findAll();
        return $this->render('type/lister.html.twig', [
            'pTypes' => $type,]);	
            
    }

}
