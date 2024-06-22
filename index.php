<?php
declare(strict_types=1);

namespace App;

use App\EsgiAlgorithmie\Bibliotheque;

require_once 'vendor/autoload.php';

$bibliotheque = new Bibliotheque();

// Définit les options du menu
const MENU_OPTIONS = [
    '1' => 'Ajouter un livre',
    '2' => 'Modifier un livre',
    '3' => 'Supprimer un livre',
    '4' => 'Afficher les livres',
    '5' => 'Afficher un livre',
    '6' => 'Trier les livres',
    '7' => 'Rechercher un livre',
    '8' => 'Afficher l\'historique',
    '9' => 'Quitter',
];

// Boucle principale du menu
while (true) {
    echo "\nMenu :\n";
    foreach (MENU_OPTIONS as $key => $value) {
        echo "{$key}. {$value}\n";
    }

    $choix = readline("Votre choix : ");

    if (!array_key_exists($choix, MENU_OPTIONS)) {
        echo "Choix invalide. Veuillez choisir une option valide.\n";
        continue;
    }

    switch ($choix) {
        case '1':
            $nom = readline("Nom du livre : ");
            $description = readline("Description du livre : ");
            $disponible = readline("Le livre est-il disponible en stock ? (Oui/Non) : ");
            $disponible = strtolower($disponible) === "oui" ? true : false;
            $bibliotheque->ajouterLivre($nom, $description, $disponible);
            break;
        case '2':
            $id = readline("ID du livre à modifier : ");
            $nom = readline("Nouveau nom du livre : ");
            $description = readline("Nouvelle description du livre : ");
            $disponible = readline("Le livre est-il toujours disponible en stock ? (Oui/Non) : ");
            $disponible = strtolower($disponible) === "oui" ? true : false;
            $bibliotheque->modifierLivre($id, $nom, $description, $disponible);
            break;
        case '3':
            $id = readline("ID du livre à supprimer : ");
            $bibliotheque->supprimerLivre($id);
            break;
        case '4':
            $bibliotheque->afficherLivres();
            break;
        case '5':
            $id = readline("ID du livre à afficher : ");
            $bibliotheque->afficherLivre($id);
            break;
        case '6':
            $colonne = readline("Trier par quelle colonne ? (nom/description/disponible) : ");
            $ordre = readline("Ordre de tri (asc/desc) : ");
            $bibliotheque->trierLivres($colonne, $ordre);
            break;
        case '7':
            $colonne = readline("Rechercher sur quelle colonne ? (nom/description/disponible/id) : ");
            $valeur = readline("Valeur à rechercher : ");
            $livreTrouve = $bibliotheque->rechercherLivre($colonne, $valeur);
            if ($livreTrouve !== null) {
                echo "Livre trouvé :\n";
                $bibliotheque->afficherLivre($livreTrouve->id);
            } else {
                echo "Livre non trouvé.\n";
            }
            break;
        case '8':
            $bibliotheque->afficherHistorique();
            break;
        case '9':
            echo "Au revoir !\n";
            exit;
    }
}
