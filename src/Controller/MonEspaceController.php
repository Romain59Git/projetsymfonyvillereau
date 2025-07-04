<?php
namespace App\Controller;

use App\Entity\Rencontre;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Attribute\IsGranted;

#[Route('/mon-espace')]
#[IsGranted('ROLE_LICENCIE')]
class MonEspaceController extends AbstractController
{
    #[Route('/', name: 'mon_espace')]
    public function index(EntityManagerInterface $em): Response
    {
        $rencontres = $em->getRepository(Rencontre::class)->findBy([], ['date' => 'ASC']);
        
        return $this->render('mon_espace/index.html.twig', [
            'rencontres' => $rencontres
        ]);
    }
} 