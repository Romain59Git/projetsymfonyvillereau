<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LegalController extends AbstractController
{
    #[Route('/mentions-legales', name: 'mentions_legales')]
    public function mentionsLegales(): Response
    {
        return $this->render('legal/mentions_legales.html.twig');
    }

    #[Route('/confidentialite', name: 'confidentialite')]
    public function confidentialite(): Response
    {
        return $this->render('legal/confidentialite.html.twig');
    }

    #[Route('/accessibilite', name: 'accessibilite')]
    public function accessibilite(): Response
    {
        return $this->render('legal/accessibilite.html.twig');
    }

    #[Route('/cookies', name: 'cookies')]
    public function cookies(): Response
    {
        return $this->render('legal/cookies.html.twig');
    }
} 