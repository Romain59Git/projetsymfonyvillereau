<?php
namespace App\Controller\Admin;

use App\Entity\Rencontre;
use App\Form\RencontreTypeForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/admin/rencontres")
 * @IsGranted("ROLE_ADMIN")
 */
class RencontreAdminController extends AbstractController
{
    /**
     * @Route("/", name="admin_rencontre_index")
     */
    public function index(EntityManagerInterface $em): Response
    {
        $rencontres = $em->getRepository(Rencontre::class)->findAll();
        return $this->render('admin/rencontre/index.html.twig', [
            'rencontres' => $rencontres
        ]);
    }

    /**
     * @Route("/nouvelle", name="admin_rencontre_new")
     */
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $rencontre = new Rencontre();
        $form = $this->createForm(RencontreTypeForm::class, $rencontre);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($rencontre);
            $em->flush();
            return $this->redirectToRoute('admin_rencontre_index');
        }
        return $this->render('admin/rencontre/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
} 