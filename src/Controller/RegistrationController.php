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


use Symfony\Component\Security\Core\Exception\AccessDeniedException;


use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;


class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, 
    MailerInterface $mailer): Response
    {
        $user = new Compte();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                // encode the plain password
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );

                $numAdherent = $form->get('num_adherent')->getData();
                if ($numAdherent == null || $numAdherent == "") {

                    $lastUser = $entityManager->getRepository(Compte::class)->findOneBy([], ['id' => 'DESC']);
                    $numAdherent = '9999001'; // Valeur par défaut si aucun utilisateur précédent n'existe

                    if ($lastUser) {
                        $lastNumAdherent = $lastUser->getNumAdherent();
                        $lastNumber = intval(substr($lastNumAdherent, 4)); // Obtenir la partie numérique du numéro d'adhérent précédent
                        $nextNumber = $lastNumber + 1;
                        $numAdherent = '9999' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
                    }
                }

                $user->setNumAdherent($numAdherent);
                $user->setRoles(['ROLE_USER']);
                $user->setActif(false);
                $user->setArchive(false);
                $user->setDateAdhesion(new \DateTime());

                $entityManager->persist($user);
                $entityManager->flush();

                $this->sendRegistrationEmail($user, $mailer);

                return $this->redirectToRoute('route_accueil');
            }
        } catch (\Exception $e) {
            return $this->forward('App\Controller\ErrorController::showErrorAuthentification', [
                'exception' => new AccessDeniedException("Erreur d'inscription, contactez un administrateur.")
            ]);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    private function sendRegistrationEmail(Compte $user, MailerInterface $mailer): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address('contact@selmoment.fr', 'SEL Moment !')) // Remplacez par votre adresse e-mail
            ->to('selmomentvilledieu@gmail.com') // Adresse e-mail de réception
            ->subject('Nouvelle inscription au site')
            ->htmlTemplate('email/newAdherent.html.twig') // Template Twig pour l'e-mail
            ->context(['user' => $user]);

        $mailer->send($email);
    }

}
