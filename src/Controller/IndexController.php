<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Annonce;
use App\Entity\Evenement;

class IndexController extends AbstractController
{
    #[Route('', name: 'app_index')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Annonce::class);
        $annonce = $repository->findAll();

        $currentDate = (new \DateTime());

        $nextEvent = $doctrine->getRepository(Evenement::class)->createQueryBuilder('e')
            ->andWhere('e.date_ev >= :currentDate')
            ->setParameter('currentDate', $currentDate)
            ->orderBy('e.date_ev', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        $latestEvents = $doctrine->getRepository(Evenement::class)
            ->createQueryBuilder('e')
            ->andWhere('e.date_ev < :currentDate')
            ->setParameter('currentDate', $currentDate)
            ->orderBy('e.date_ev', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();

        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'pAnnonces' => $annonce,
            'nextEvent' => $nextEvent,
            'latestEvents' => $latestEvents,
        ]);
    }

    public function admin(ManagerRegistry $doctrine){
        $repository = $doctrine->getRepository(Categorie::class);
        $cat = $repository->findAll();



        return $this->render('admin/index.html.twig', [
            'pCategorie' => $cat,
            'pGenre' => $genre,
            'pLivre' => $livre,]);	
            
    }
}
