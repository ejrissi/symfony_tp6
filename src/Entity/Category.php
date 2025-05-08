<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, Article>
     */
    #[ORM\OneToMany(targetEntity: Article::class, mappedBy: 'category')]
    private Collection $article;

    /**
     * @var Collection<int, CategorySearch>
     */
    #[ORM\OneToMany(targetEntity: CategorySearch::class, mappedBy: 'category')]
    private Collection $categorySearches;

   

    public function __construct()
    {
        $this->article = new ArrayCollection();
        $this->categorySearches = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticle(): Collection
    {
        return $this->article;
    }

    public function addArticle(Article $article): static
    {
        if (!$this->article->contains($article)) {
            $this->article->add($article);
            $article->setCategory($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): static
    {
        if ($this->article->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getCategory() === $this) {
                $article->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CategorySearch>
     */
    public function getCategorySearches(): Collection
    {
        return $this->categorySearches;
    }

    public function addCategorySearch(CategorySearch $categorySearch): static
    {
        if (!$this->categorySearches->contains($categorySearch)) {
            $this->categorySearches->add($categorySearch);
            $categorySearch->setCategory($this);
        }

        return $this;
    }

    public function removeCategorySearch(CategorySearch $categorySearch): static
    {
        if ($this->categorySearches->removeElement($categorySearch)) {
            // set the owning side to null (unless already changed)
            if ($categorySearch->getCategory() === $this) {
                $categorySearch->setCategory(null);
            }
        }

        return $this;
    }

   
}
