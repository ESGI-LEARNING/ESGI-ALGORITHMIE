<?php

declare(strict_types=1);


use App\EsgiAlgorithmie\Actions\Library\LibraryAction;
use App\EsgiAlgorithmie\Bibliotheque;
use App\EsgiAlgorithmie\Console\Console;
use App\EsgiAlgorithmie\Enum\ConsoleEnum;

require_once 'vendor/autoload.php';

$bibliotheque = new Bibliotheque();
$bookAction = new LibraryAction();
$console = new Console();


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
         $console->errorMessage("Choix invalide. Veuillez choisir une option valide.\n");
        continue;
    }

    switch ($choix) {
        case '1':
            $name = $console->read(ConsoleEnum::String, "Nom du livre : ");
            $description = $console->read(ConsoleEnum::String, "Description du livre : ");
            $is_available = $console->read(ConsoleEnum::Boolean, "Le livre est-il disponible en stock ? (Yes/No) : ", 'no');
            $bookAction->create($name, $description, $is_available);
            break;
        case '2':
            $id = $console->read(ConsoleEnum::Integer, "ID du livre à modifier : ");;
            $name = $console->read(ConsoleEnum::String, "Nom du livre : ");
            $description = $console->read(ConsoleEnum::String, "Description du livre : ");
            $is_available = $console->read(ConsoleEnum::Boolean, "Le livre est-il disponible en stock ? (Yes/No) : ", 'no');

            $bookAction->update($id, $name, $description, $is_available);
            break;
        case '3':
            $id = $console->read(ConsoleEnum::String, "ID du livre à supprimer : ");
            $bookAction->delete($id);
            break;
        case '4':
            $bookAction->getAll();
            break;
        case '5':
            $id = $console->read(ConsoleEnum::String, "ID du livre à afficher : ");
            $bookAction->get($id);
            break;
        case '6':
            $col = $console->read(ConsoleEnum::String, "Trier par quelle colonne ? (nom/description/disponible) : ");
            $order = $console->read(ConsoleEnum::String, "Ordre de tri (asc/desc) :");
            $bibliotheque->trierLivres($col, $order);
            break;
        case '7':
            $col = $console->read(ConsoleEnum::String, "Rechercher sur quelle colonne ? (nom/description/disponible/id) : ");
            $value = $console->read(ConsoleEnum::String, "Valeur à rechercher :");
            $bookFind = $bibliotheque->rechercherLivre($col, $value);
            if ($bookFind !== null) {
                echo "Livre trouvé :\n";
                $bookAction->get($bookFind->id);
            } else {
                $console->errorMessage("Aucun livre trouvé.");
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
