<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageJuridiqueController extends AbstractController
{
    public function mentionsLegales(): Response
    {
        return $this->render('page_juridique/mentions_legales.html.twig', [
            'controller_name' => 'PageJuridiqueController',
        ]);
    }

    public function politiqueConfidentialite(): Response
    {
        return $this->render('page_juridique/politique_confidentialite.html.twig', [
            'controller_name' => 'PageJuridiqueController',
        ]);
    }
}
