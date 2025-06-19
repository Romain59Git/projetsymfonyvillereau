<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Repository\AvisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AvisController extends AbstractController
{
    #[Route('/avis', name: 'app_avis')]
    public function index(Request $request, EntityManagerInterface $entityManager, AvisRepository $avisRepository): Response
    {
        // Gérer la soumission du formulaire
        if ($request->isMethod('POST')) {
            $avis = new Avis();
            $avis->setName($request->request->get('name'))
                 ->setRating((int) $request->request->get('rating'))
                 ->setComment($request->request->get('comment'));

            $entityManager->persist($avis);
            $entityManager->flush();

            $this->addFlash('success', 'Merci pour votre avis !');

            return $this->redirectToRoute('app_avis');
        }

        // Récupérer tous les avis pour l'affichage
        $avis = $avisRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('avis/index.html.twig', [
            'avis' => $avis,
        ]);
    }
}
