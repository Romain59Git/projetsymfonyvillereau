<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/mon-espace", name="mon_espace")
 * @IsGranted("ROLE_LICENCIE")
 */
class MonEspaceController extends AbstractController
{
    /**
     * @Route("/mon-espace", name="mon_espace")
     * @IsGranted("ROLE_LICENCIE")
     */
    public function index(): Response
    {
        // Les données seront injectées plus tard
        return $this->render('mon_espace/index.html.twig');
    }
} 