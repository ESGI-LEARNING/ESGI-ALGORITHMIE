<?php

namespace App\EsgiAlgorithmie;

use JsonException;

final class Bibliotheque
{
    /** @var string Nom du fichier pour stocker les livres */
    private const FICHIER_LIVRES = "livres.json";
    /** @var Livre[] Tableau contenant les livres de la bibliothèque */
    private array $livres = [];
    /** @var string[] Tableau contenant l'historique des actions effectuées */
    private array $historique = [];
    /** @var int Dernier ID utilisé pour un livre */
    private int $dernierID = 0;

    /**
     * Constructeur de la classe Bibliotheque
     * Charge les livres depuis le fichier JSON au démarrage
     */
    public function __construct()
    {
        $this->chargerLivres();
    }

    /**
     * Charge les livres depuis le fichier JSON
     */
    private function chargerLivres(): void
    {
        if (file_exists(self::FICHIER_LIVRES)) {
            try {
                $jsonData = file_get_contents(self::FICHIER_LIVRES);
                $arrayData = json_decode($jsonData, true, 512, JSON_THROW_ON_ERROR);

                if (is_array($arrayData)) {
                    foreach ($arrayData as $data) {
                        $livre = new Livre($data['id'], $data['nom'], $data['description'], $data['disponible']);
                        $this->livres[$livre->id] = $livre;
                        $this->dernierID = max($this->dernierID, (int)$livre->id);
                    }
                }
            } catch (JsonException $e) {
                echo "Erreur lors du chargement des livres : " . $e->getMessage() . "\n";
            }
        }
    }

    /**
     * Ajoute un livre à la bibliothèque
     */
    public function ajouterLivre(string $nom, string $description, bool $disponible): void
    {
        $this->dernierID++;
        $id = (string)$this->dernierID;
        $livre = new Livre($id, $nom, $description, $disponible);
        $this->livres[$id] = $livre;
        $this->sauvegarderLivres();
        $this->enregistrerAction("Ajout du livre '$nom'");
    }

    /**
     * Sauvegarde les livres dans un fichier JSON
     */
    private function sauvegarderLivres(): void
    {
        try {
            $jsonData = json_encode(array_values($this->livres), JSON_THROW_ON_ERROR);
            file_put_contents(self::FICHIER_LIVRES, $jsonData);
        } catch (JsonException $e) {
            echo "Erreur lors de la sauvegarde des livres : " . $e->getMessage() . "\n";
        }
    }

    /**
     * Enregistre une action dans l'historique
     */
    private function enregistrerAction(string $action): void
    {
        $this->historique[] = date('Y-m-d H:i:s') . " - " . $action;
    }

    /**
     * Modifie un livre dans la bibliothèque
     */
    public function modifierLivre(string $id, string $nom, string $description, bool $disponible): void
    {
        if (isset($this->livres[$id])) {
            $livre = $this->livres[$id];
            $livre->nom = $nom;
            $livre->description = $description;
            $livre->disponible = $disponible;
            $this->sauvegarderLivres();
            $this->enregistrerAction("Modification du livre '$nom'");
        } else {
            echo "Livre introuvable.\n";
        }
    }

    /**
     * Supprime un livre de la bibliothèque
     */
    public function supprimerLivre(string $id): void
    {
        if (isset($this->livres[$id])) {
            $nom = $this->livres[$id]->nom;
            unset($this->livres[$id]);
            $this->sauvegarderLivres();
            $this->enregistrerAction("Suppression du livre '$nom'");
        } else {
            echo "Livre introuvable.\n";
        }
    }

    /**
     * Affiche la liste des livres de la bibliothèque
     */
    public function afficherLivres(): void
    {
        echo "Liste des livres :\n";
        foreach ($this->livres as $livre) {
            echo "ID: {$livre->id}, Nom: {$livre->nom}, Description: {$livre->description}, Disponible: " . ($livre->disponible ? "Oui" : "Non") . "\n";
        }
    }

    /**
     * Affiche les détails d'un livre
     */
    public function afficherLivre(string $id): void
    {
        if (isset($this->livres[$id])) {
            $livre = $this->livres[$id];
            echo "ID: {$livre->id}, Nom: {$livre->nom}, Description: {$livre->description}, Disponible: " . ($livre->disponible ? "Oui" : "Non") . "\n";
        } else {
            echo "Livre introuvable.\n";
        }
    }

    /**
     * Trie les livres selon une colonne et un ordre donnés en utilisant le tri fusion
     */
    public function trierLivres(string $colonne, string $ordre = "asc"): void
    {
        $livresArray = array_values($this->livres);
        $this->triFusion($livresArray, $colonne, $ordre);
        $this->livres = array_combine(array_column($livresArray, 'id'), $livresArray);
        $this->sauvegarderLivres();
        $this->enregistrerAction("Tri des livres par '$colonne' ($ordre)");
    }

    /**
     * Implémentation du tri fusion
     */
    private function triFusion(array &$livres, string $colonne, string $ordre): void
    {
        if (count($livres) <= 1) {
            return;
        }

        $milieu = floor(count($livres) / 2);
        $gauche = array_slice($livres, 0, $milieu);
        $droite = array_slice($livres, $milieu);

        $this->triFusion($gauche, $colonne, $ordre);
        $this->triFusion($droite, $colonne, $ordre);

        $this->fusionner($livres, $gauche, $droite, $colonne, $ordre);
    }

    /**
     * Fusionne deux sous-tableaux triés
     */
    private function fusionner(array &$livres, array $gauche, array $droite, string $colonne, string $ordre): void
    {
        $i = 0;
        $j = 0;
        $k = 0;

        while ($i < count($gauche) && $j < count($droite)) {
            $comparaison = $this->comparerLivres($gauche[$i], $droite[$j], $colonne);
            if (($ordre === "asc" && $comparaison <= 0) || ($ordre === "desc" && $comparaison > 0)) {
                $livres[$k] = $gauche[$i];
                $i++;
            } else {
                $livres[$k] = $droite[$j];
                $j++;
            }
            $k++;
        }

        while ($i < count($gauche)) {
            $livres[$k] = $gauche[$i];
            $i++;
            $k++;
        }

        while ($j < count($droite)) {
            $livres[$k] = $droite[$j];
            $j++;
            $k++;
        }
    }

    /**
     * Compare deux livres selon une colonne donnée
     */
    private function comparerLivres(Livre $a, Livre $b, string $colonne): int
    {
        return strcmp($a->{$colonne}, $b->{$colonne});
    }

    /**
     * Recherche un livre dans la bibliothèque en utilisant le tri rapide et la recherche binaire
     */
    public function rechercherLivre(string $colonne, string $valeur): ?Livre
    {
        $livresArray = array_values($this->livres);
        $this->triRapide($livresArray, 0, count($livresArray) - 1, $colonne);

        $gauche = 0;
        $droite = count($livresArray) - 1;

        while ($gauche <= $droite) {
            $milieu = floor(($gauche + $droite) / 2);
            $comparaison = strcmp($livresArray[$milieu]->{$colonne}, $valeur);
            if ($comparaison == 0) {
                return $livresArray[$milieu];
            }
            if ($comparaison < 0) {
                $gauche = $milieu + 1;
            } else {
                $droite = $milieu - 1;
            }
        }
        return null;
    }

    /**
     * Implémentation du tri rapide
     */
    private function triRapide(array &$livres, int $debut, int $fin, string $colonne): void
    {
        if ($debut < $fin) {
            $pivot = $this->partition($livres, $debut, $fin, $colonne);
            $this->triRapide($livres, $debut, $pivot - 1, $colonne);
            $this->triRapide($livres, $pivot + 1, $fin, $colonne);
        }
    }

    /**
     * Partitionne le tableau pour le tri rapide
     */
    private function partition(array &$livres, int $debut, int $fin, string $colonne): int
    {
        $pivot = $livres[$fin];
        $i = $debut - 1;

        for ($j = $debut; $j < $fin; $j++) {
            if ($this->comparerLivres($livres[$j], $pivot, $colonne) <= 0) {
                $i++;
                $temp = $livres[$i];
                $livres[$i] = $livres[$j];
                $livres[$j] = $temp;
            }
        }

        $temp = $livres[$i + 1];
        $livres[$i + 1] = $livres[$fin];
        $livres[$fin] = $temp;

        return $i + 1;
    }

    /**
     * Affiche l'historique des actions effectuées
     */
    public function afficherHistorique(): void
    {
        echo "Historique des actions :\n";
        foreach ($this->historique as $action) {
            echo $action . "\n";
        }
    }
}