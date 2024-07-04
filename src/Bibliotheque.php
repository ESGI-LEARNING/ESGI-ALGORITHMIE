<?php

namespace App\EsgiAlgorithmie;

use App\EsgiAlgorithmie\Models\Livre;
use JsonException;

final class Bibliotheque
{

    /** @var Livre[] Tableau contenant les livres de la bibliothèque */
    private array $livres = [];

    /**
     * Constructeur de la classe Bibliotheque
     * Charge les livres depuis le fichier JSON au démarrage
     */
    public function __construct()
    {
        $this->chargerLivres();
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