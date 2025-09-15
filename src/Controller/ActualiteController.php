<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ActualiteController extends AbstractController
{
    #[Route('/actualite', name: 'app_actualite')]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('actualite/index.html.twig', [
            'articles' => $articleRepository->findLatest(),
        ]);
    }
}
