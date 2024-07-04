<?php

namespace App\EsgiAlgorithmie\Console;

class ConsoleBook
{
    public static function displayBooks(array $data): void
    {
        if ($data != null) {
            foreach ($data as $book) {
                echo "ID: {$book['id']}, Nom: {$book['name']}, Description: {$book['description']}, Disponible: " . ($book['is_available'] ? "Oui" : "Non") . "\n";
            }
        } else {
            (new Console)->errorMessage("Aucun livre n'est disponible.");
        }
    }

    public static function displayBook(array $data): void
    {
        if ($data != null) {
            echo "ID: {$data['id']}, Nom: {$data['name']}, Description: {$data['description']}, Disponible: " . ($data['is_available'] ? "Oui" : "Non") . "\n";
        } else {
            (new Console)->errorMessage("Le livre n'existe pas.");
        }
    }
}