<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Traitement du formulaire de contact
        if ($request->isMethod('POST')) {
            $contact = new Contact();
            $contact->setName($request->request->get('name'))
                   ->setEmail($request->request->get('email'))
                   ->setPhone($request->request->get('phone'))
                   ->setSubject($request->request->get('subject'))
                   ->setMessage($request->request->get('message'));

            $entityManager->persist($contact);
            $entityManager->flush();

            $this->addFlash('success', 'Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.');

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/index.html.twig');
    }
}
