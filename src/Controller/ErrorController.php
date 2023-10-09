<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
    #[Route('/error', name: 'app_error')]
    public function index(): Response
    {
        return $this->render('error/index.html.twig', [
            'controller_name' => 'ErrorController',
        ]);
    }

    public function showAccessDenied(\Throwable $exception): Response
    {
        return $this->render('error/access_denied.html.twig', [
            'exception' => $exception,
        ]);
    }

    public function showErrorAuthentification(\Throwable $exception): Response
    {
        return $this->render('error/error_authentification.html.twig', [
            'exception' => $exception,
        ]);
    }
}
