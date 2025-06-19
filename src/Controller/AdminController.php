<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Licencie;
use App\Entity\User;
use App\Entity\Article;
use App\Entity\Avis;
use App\Form\LicencieTypeForm;
use App\Form\ArticleTypeForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/admin')]
final class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin')]
    public function index(EntityManagerInterface $em): Response
    {
        // Récupération des statistiques
        $stats = [
            'articles' => $em->getRepository(Article::class)->count([]),
            'licencies' => $em->getRepository(Licencie::class)->count([]),
            'avis' => $em->getRepository(Avis::class)->count([]),
            'users' => $em->getRepository(User::class)->count([]),
            'recent_avis' => $em->getRepository(Avis::class)->findBy([], ['createdAt' => 'DESC'], 5),
            'recent_licencies' => $em->getRepository(Licencie::class)->findBy([], ['id' => 'DESC'], 5)
        ];

        return $this->render('admin/index.html.twig', [
            'stats' => $stats
        ]);
    }

    #[Route('/licencie/new', name: 'admin_licencie_new')]
    public function newLicencie(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher, MailerInterface $mailer): Response
    {
        $licencie = new Licencie();
        $form = $this->createForm(LicencieTypeForm::class, $licencie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Création du User avec le mot de passe saisi
            $user = new User();
            $user->setEmail($licencie->getEmail());
            $user->setRoles(['ROLE_LICENCIE']);
            $user->setPassword($hasher->hashPassword($user, $form->get('plainPassword')->getData()));
            $user->setMustChangePassword(true);
            $em->persist($user);
            
            // Association Licencie <-> User
            $licencie->setUser($user);
            $em->persist($licencie);
            $em->flush();
            
            // Envoi du mail de confirmation
            $email = (new Email())
                ->from('admin@cdjvillereau.fr')
                ->to($user->getEmail())
                ->subject('Votre compte CDJ Villereau')
                ->text("Bienvenue !\n\nVotre compte a été créé avec succès.\n\nEmail : " . $user->getEmail() . "\n\nVous devrez changer votre mot de passe lors de votre première connexion à votre espace licencié sur notre site.");
            $mailer->send($email);

            $this->addFlash('success', 'Le licencié a été créé avec succès !');
            return $this->redirectToRoute('admin_licencies');
        }

        return $this->render('admin/licencie_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/licencies', name: 'admin_licencies')]
    public function licencies(EntityManagerInterface $em): Response
    {
        $licencies = $em->getRepository(Licencie::class)->findAll();
        
        return $this->render('admin/licencies.html.twig', [
            'licencies' => $licencies
        ]);
    }

    #[Route('/licencie/{id}/delete', name: 'admin_licencie_delete', methods: ['POST'])]
    public function deleteLicencie(Request $request, Licencie $licencie, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$licencie->getId(), $request->request->get('_token'))) {
            $user = $licencie->getUser();
            if ($user) {
                $em->remove($user);
            }
            $em->remove($licencie);
            $em->flush();
            $this->addFlash('success', 'Licencié supprimé avec succès !');
        }

        return $this->redirectToRoute('admin_licencies');
    }

    #[Route('/articles', name: 'admin_articles')]
    public function articles(EntityManagerInterface $em): Response
    {
        $articles = $em->getRepository(Article::class)->findAll();
        
        return $this->render('admin/articles.html.twig', [
            'articles' => $articles
        ]);
    }

    #[Route('/article/new', name: 'admin_article_new')]
    public function newArticle(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleTypeForm::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                    $article->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image');
                }
            }

            $em->persist($article);
            $em->flush();

            $this->addFlash('success', 'Article créé avec succès !');
            return $this->redirectToRoute('admin_articles');
        }

        return $this->render('admin/article_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Nouvel article'
        ]);
    }

    #[Route('/article/{id}/edit', name: 'admin_article_edit')]
    public function editArticle(Request $request, Article $article, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ArticleTypeForm::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                    
                    // Supprimer l'ancienne image si elle existe
                    if ($article->getImage()) {
                        $oldImagePath = $this->getParameter('images_directory').'/'.$article->getImage();
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }
                    
                    $article->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image');
                }
            }

            $em->flush();

            $this->addFlash('success', 'Article modifié avec succès !');
            return $this->redirectToRoute('admin_articles');
        }

        return $this->render('admin/article_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Modifier l\'article',
            'article' => $article
        ]);
    }

    #[Route('/article/{id}/delete', name: 'admin_article_delete', methods: ['POST'])]
    public function deleteArticle(Request $request, Article $article, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            // Supprimer l'image si elle existe
            if ($article->getImage()) {
                $imagePath = $this->getParameter('images_directory').'/'.$article->getImage();
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $em->remove($article);
            $em->flush();

            $this->addFlash('success', 'Article supprimé avec succès !');
        }

        return $this->redirectToRoute('admin_articles');
    }

    #[Route('/avis', name: 'admin_avis')]
    public function avis(EntityManagerInterface $em): Response
    {
        $avis = $em->getRepository(Avis::class)->findBy([], ['createdAt' => 'DESC']);
        
        return $this->render('admin/avis.html.twig', [
            'avis' => $avis
        ]);
    }

    #[Route('/avis/{id}/delete', name: 'admin_avis_delete', methods: ['POST'])]
    public function deleteAvis(Request $request, Avis $avis, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$avis->getId(), $request->request->get('_token'))) {
            $em->remove($avis);
            $em->flush();
            $this->addFlash('success', 'Avis supprimé avec succès !');
        }

        return $this->redirectToRoute('admin_avis');
    }

    #[Route('/parametres', name: 'admin_parametres')]
    public function parametres(): Response
    {
        return $this->render('admin/parametres.html.twig');
    }
}
