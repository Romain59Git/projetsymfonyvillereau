<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Licencie;
use App\Entity\User;
use App\Form\LicencieTypeForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher, MailerInterface $mailer): Response
    {
        $licencie = new Licencie();
        $form = $this->createForm(LicencieTypeForm::class, $licencie);
        $form->handleRequest($request);
        $success = false;
        if ($form->isSubmitted() && $form->isValid()) {
            // Génération d'un mot de passe aléatoire
            $plainPassword = bin2hex(random_bytes(5));
            // Création du User
            $user = new User();
            $user->setEmail($licencie->getEmail());
            $user->setRoles(['ROLE_LICENCIE']);
            $user->setPassword($hasher->hashPassword($user, $plainPassword));
            $em->persist($user);
            // Association Licencie <-> User
            $licencie->setUser($user);
            $em->persist($licencie);
            $em->flush();
            // Envoi du mail
            $email = (new Email())
                ->from('admin@cdjvillereau.fr')
                ->to($user->getEmail())
                ->subject('Votre compte CDJ Villereau')
                ->text("Bienvenue !\nVotre compte a été créé.\nEmail : " . $user->getEmail() . "\nMot de passe : " . $plainPassword . "\nVous pouvez le modifier après connexion.");
            $mailer->send($email);
            $success = true;
        }
        return $this->render('admin/index.html.twig', [
            'form_licencie' => $form->createView(),
            'success' => $success,
        ]);
    }
}
