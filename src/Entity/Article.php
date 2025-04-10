<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Le nom de l'article ne peut pas être vide.")]
    #[Assert\Length(
        min: 5,
        max: 50,
        minMessage: "Le nom de l'article doit comporter au moins {{ limit }} caractères.",
        maxMessage: "Le nom de l'article ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $Nom = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]  
    #[Assert\NotBlank(message: "Le prix ne peut pas être vide.")]
    #[Assert\GreaterThan(
        value: 0,
        message: "Le prix doit être supérieur à zéro."
    )]
    private ?float $Prix = null;  

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(?string $Nom): static
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getPrix(): ?float 
    {
        return $this->Prix;
    }

    public function setPrix(float $Prix): static  
    {
        $this->Prix = $Prix;

        return $this;
    }
}
