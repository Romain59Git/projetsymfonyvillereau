<?php
namespace App\Controller\Admin;

use App\Entity\Rencontre;
use App\Form\RencontreTypeForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/rencontres')]
#[IsGranted('ROLE_ADMIN')]
class RencontreAdminController extends AbstractController
{
    #[Route('/', name: 'admin_rencontre_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $rencontres = $em->getRepository(Rencontre::class)->findAll();
        return $this->render('admin/rencontre/index.html.twig', [
            'rencontres' => $rencontres
        ]);
    }

    #[Route('/nouvelle', name: 'admin_rencontre_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $rencontre = new Rencontre();
        $form = $this->createForm(RencontreTypeForm::class, $rencontre);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($rencontre);
            $em->flush();
            $this->addFlash('success', 'Rencontre créée avec succès.');
            return $this->redirectToRoute('admin_rencontre_index');
        }
        return $this->render('admin/rencontre/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/modifier', name: 'admin_rencontre_edit')]
    public function edit(Request $request, Rencontre $rencontre, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(RencontreTypeForm::class, $rencontre);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Rencontre modifiée avec succès.');
            return $this->redirectToRoute('admin_rencontre_index');
        }
        return $this->render('admin/rencontre/edit.html.twig', [
            'form' => $form->createView(),
            'rencontre' => $rencontre
        ]);
    }

    #[Route('/{id}/supprimer', name: 'admin_rencontre_delete', methods: ['POST'])]
    public function delete(Request $request, Rencontre $rencontre, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rencontre->getId(), $request->request->get('_token'))) {
            $em->remove($rencontre);
            $em->flush();
            $this->addFlash('success', 'Rencontre supprimée avec succès.');
        }
        return $this->redirectToRoute('admin_rencontre_index');
    }
} 