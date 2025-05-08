<?php

namespace App\Entity;


class PropertySearch
{
   
    private ?string $nom = null;

   
    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }
}
