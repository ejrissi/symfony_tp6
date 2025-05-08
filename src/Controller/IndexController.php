<?php
namespace App\Controller;
use App\Form\ArticleType;
use App\Form\CategoryType;
use App\Form\PropertySearchType;
use App\Form\CategorySearchType;
use App\Form\PriceSearchType;
use App\Entity\Article;
use App\Entity\PriceSearch;
use App\Entity\CategorySearch;
use App\Entity\PropertySearch;
use App\Entity\Category;
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
    public function home(Request $request): Response
    {
        $propertySearch = new PropertySearch();
        // Create the form
        $form = $this->createForm(PropertySearchType::class, $propertySearch);
        $form->handleRequest($request);
    
        // Initially, no articles are fetched until the form is submitted
        $articles = [];
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Get the search term from the form
            $nom = $propertySearch->getNom();
    
            // If the user provided a name, search for articles by that name
            if ($nom !== "") {
                $articles = $this->entityManager->getRepository(Article::class)->findBy(['Nom' => $nom]);
            } else {
                // If no name is provided, fetch all articles
                $articles = $this->entityManager->getRepository(Article::class)->findAll();
            }
        }
    
        // Render the template and pass the form and articles
        return $this->render('articles/index.html.twig', [
            'form' => $form->createView(),
            'articles' => $articles,
        ]);
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

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Article modifié avec succès.');
            return $this->redirectToRoute('article_list');
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


#[Route('/category/newCat', name: 'new_category', methods: ['GET', 'POST'])]
    public function newCategory(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'Catégorie ajoutée avec succès !');

            return $this->redirectToRoute('new_category');
        }

        return $this->render('articles/newCategory.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    #[Route('/art_cat', name: 'article_par_cat', methods: ['GET', 'POST'])]
public function articlesParCategorie(Request $request): Response
{
    $categorySearch = new CategorySearch();
    $form = $this->createForm(CategorySearchType::class, $categorySearch);
    $form->handleRequest($request);

    $articles = [];

    if ($form->isSubmitted() && $form->isValid()) {
        $category = $categorySearch->getCategory();

        if ($category !== null) {
            $articles = $category->getArticle(); // assumes relation OneToMany
        } else {
            $articles = $this->getDoctrine()
                ->getRepository(Article::class)
                ->findAll();
        }
    }

    return $this->render('articles/articlesParCategorie.html.twig', [
        'form' => $form->createView(),
        'articles' => $articles,
    ]);
}



#[Route('/art_prix', name: 'article_par_prix', methods: ['GET', 'POST'])]
public function articlesParPrix(Request $request)
{
    $priceSearch = new PriceSearch();
    $form = $this->createForm(PriceSearchType::class, $priceSearch);
    $form->handleRequest($request);
    $articles = [];

    if ($form->isSubmitted() && $form->isValid()) {
        $minPrice = $priceSearch->getMinPrice();
        $maxPrice = $priceSearch->getMaxPrice();
        
        // Get the repository from the entity manager
        $articleRepository = $this->entityManager->getRepository(Article::class);

        // Call the findByPriceRange method on the repository
        $articles = $articleRepository->findByPriceRange($minPrice, $maxPrice);
    }

    return $this->render('articles/articlesParPrix.html.twig', [
        'form' => $form->createView(),
        'articles' => $articles
    ]);
}



}