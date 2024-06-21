<?php

namespace App\EsgiAlgorithmie;

final class Bibliotheque
{
    private array $livres = []; // Tableau contenant les livres de la bibliothèque
    private array $historique = []; // Tableau contenant l'historique des actions effectuées

    // Constructeur de la classe Bibliotheque
    public function __construct()
    {
        // Charger les livres depuis le fichier JSON au démarrage
        $this->chargerLivres();
    }

    // Sauvegarde les livres dans un fichier JSON
    private function sauvegarderLivres(): void
    {
        file_put_contents("livres.json", json_encode($this->livres));
    }

    // Charge les livres depuis le fichier JSON
    private function chargerLivres(): void
    {
        if (file_exists("livres.json")) {
            $jsonData = file_get_contents("livres.json");
            $arrayData = json_decode($jsonData, true);

            if (is_array($arrayData)) {
                foreach ($arrayData as $data) {
                    $livre = new Livre($data['id'], $data['nom'], $data['description'], $data['disponible']);
                    $this->livres[] = $livre; // Ajoute simplement le livre au tableau sans utiliser la clé
                }
            }
        }
    }

    // Enregistre une action dans l'historique
    private function enregistrerAction(string $action): void
    {
        $this->historique[] = $action;
    }

    // Ajoute un livre à la bibliothèque
    public function ajouterLivre(string $nom, string $description, bool $disponible): void
    {
        $id = uniqid();
        $livre = new Livre($id, $nom, $description, $disponible);
        $this->livres[$id] = $livre;
        $this->sauvegarderLivres();
        $this->enregistrerAction("Ajout du livre '$nom'");
    }

    // Modifie un livre dans la bibliothèque
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

    // Supprime un livre de la bibliothèque
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

    // Affiche la liste des livres de la bibliothèque
    public function afficherLivres(): void
    {
        echo "Liste des livres :\n";
        foreach ($this->livres as $livre) {
            echo "ID: {$livre->id}, Nom: {$livre->nom}, Description: {$livre->description}, Disponible: " . ($livre->disponible ? "Oui" : "Non") . "\n";
        }
    }

    // Affiche les détails d'un livre
    public function afficherLivre(string $id): void
    {
        if (isset($this->livres[$id])) {
            $livre = $this->livres[$id];
            echo "ID: {$livre->id}, Nom: {$livre->nom}, Description: {$livre->description}, Disponible: " . ($livre->disponible ? "Oui" : "Non") . "\n";
        } else {
            echo "Livre introuvable.\n";
        }
    }

    // Trie les livres selon une colonne et un ordre donnés
    // Utilise le tri fusion (à implémenter pour respecter les exigences du sujet)
    public function trierLivres(string $colonne, string $ordre = "asc"): void
    {
        usort($this->livres, function(Livre $a, Livre $b) use ($colonne, $ordre) {
            return ($ordre === "asc") ? strcmp($a->{$colonne}, $b->{$colonne}) : strcmp($b->{$colonne}, $a->{$colonne});
        });
        $this->sauvegarderLivres();
        $this->enregistrerAction("Tri des livres par '$colonne' ($ordre)");
    }

    // Recherche un livre dans la bibliothèque
    // Utilise la recherche binaire (la liste de livres doit être triée au préalable)
    public function rechercherLivre(string $colonne, string $valeur): ?Livre
    {
        $this->trierLivres($colonne);

        $gauche = 0;
        $droite = count($this->livres) - 1;

        while ($gauche <= $droite) {
            $milieu = floor(($gauche + $droite) / 2);
            $comparaison = strcmp($this->livres[$milieu]->{$colonne}, $valeur);
            if ($comparaison == 0) {
                return $this->livres[$milieu];
            } elseif ($comparaison < 0) {
                $gauche = $milieu + 1;
            } else {
                $droite = $milieu - 1;
            }
        }
        return null;
    }

    // Affiche l'historique des actions effectuées
    public function afficherHistorique(): void
    {
        echo "Historique des actions :\n";
        foreach ($this->historique as $action) {
            echo $action . "\n";
        }
    }
}