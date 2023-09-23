<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;


class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
{
    $user = new Compte();
    $form = $this->createForm(RegistrationFormType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // encode the plain password
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $form->get('password')->getData()
            )
        );

        $lastUser = $entityManager->getRepository(Compte::class)->findOneBy([], ['id' => 'DESC']);
        $currentYear = date('Y');
        $numAdherent = $currentYear . '001'; // Valeur par défaut si aucun utilisateur précédent n'existe

        if ($lastUser) {
            $lastNumAdherent = $lastUser->getNumAdherent();
            $lastNumber = intval(substr($lastNumAdherent, 4)); // Obtenir la partie numérique du numéro d'adhérent précédent
            $nextNumber = $lastNumber + 1;
            $numAdherent = $currentYear . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        }
        

        $user->setNumAdherent($numAdherent);
        $admin->setRoles(['ROLE_USER']);
        $user->setActif(false);
        $user->setArchive(false);
        $user->setDateAdhesion(new \DateTime());

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('route_accueil');
    }

    return $this->render('registration/register.html.twig', [
        'registrationForm' => $form->createView(),
    ])
    ;
}

}
