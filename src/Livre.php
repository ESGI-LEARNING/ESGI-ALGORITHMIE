<?php

namespace App\EsgiAlgorithmie;

class Livre
{
    public string $id;
    public string $nom;
    public string $description;
    public bool $disponible;

    // Constructeur de la classe Livre
    public function __construct(string $id, string $nom, string $description, bool $disponible)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->description = $description;
        $this->disponible = $disponible;
    }
}