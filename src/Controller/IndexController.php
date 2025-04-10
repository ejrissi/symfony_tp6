<?php
namespace App\Controller;
use App\Form\ArticleType;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route; // Use Attribute instead of Annotation
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class IndexController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/article/save', name: 'save_articles')]
    public function save(): Response
    {
        $article1 = new Article();
        $article1->setNom('Article 1');
        $article1->setPrix(1000);
        $this->entityManager->persist($article1);

        $article2 = new Article();
        $article2->setNom('Article 2');
        $article2->setPrix(1500);
        $this->entityManager->persist($article2);

        $article3 = new Article();
        $article3->setNom('Article 3');
        $article3->setPrix(2000);
        $this->entityManager->persist($article3);

        $this->entityManager->flush();

        return new Response('Articles enregistrés avec ids ' . $article1->getId() . ', ' . $article2->getId() . ', ' . $article3->getId());
    }

    #[Route("/", name: "article_list")]
    public function home(): Response
    {
        $articles = $this->entityManager->getRepository(Article::class)->findAll();
        return $this->render('articles/index.html.twig', ['articles' => $articles]);
    }

    
    #[Route('/article/new', name: 'new_article', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $article = new Article();
    
        // 💡 Utilise la classe ArticleType ici
        $form = $this->createForm(ArticleType::class, $article);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            $this->entityManager->persist($article);
            $this->entityManager->flush();
    
            return $this->redirectToRoute('article_list');
        }
    
        return $this->render('articles/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // #[Route('/article/{id}', name: 'article_show', methods: ['GET', 'POST'])]
    // public function show(int $id): Response
    // {
    //     $article = $this->entityManager->getRepository(Article::class)->find($id);

    //     if (!$article) {
    //         throw $this->createNotFoundException('Article not found');
    //     }

    //     return $this->render('articles/show.html.twig', ['article' => $article]);
    // }


    
    #[Route('/article/{id}', name: 'article_show')]
    public function show(int $id): Response
    {
        // Get the repository for the Article entity
        $article = $this->entityManager->getRepository(Article::class)->find($id);

        if (!$article) {
            throw $this->createNotFoundException('Article not found');
        }

        return $this->render('articles/show.html.twig', ['article' => $article]);
    }


    // Add this function to your controller

    #[Route('/article/edit/{id}', name: 'edit_article', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        // Fetch the article to edit
        $article = $this->entityManager->getRepository(Article::class)->find($id);

        if (!$article) {
            throw $this->createNotFoundException('Article not found');
        }

        // Create the form to edit the article using ArticleType
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        // If the form is submitted and valid, persist changes to the article
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush(); // Update the article in the database
            return $this->redirectToRoute('article_list'); // Redirect to the article list
        }

        // Render the edit form
        return $this->render('articles/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }


#[Route('/article/delete/{id}', name: 'delete_article', methods: ['POST'])]
public function delete(Request $request, int $id): Response
{
    $article = $this->entityManager->getRepository(Article::class)->find($id);

    if (!$article) {
        throw $this->createNotFoundException('Article not found');
    }

    // Add CSRF protection for security
    if ($this->isCsrfTokenValid('delete-article', $request->request->get('_token'))) {
        $this->entityManager->remove($article);
        $this->entityManager->flush();
    }

    return $this->redirectToRoute('article_list');
}





}