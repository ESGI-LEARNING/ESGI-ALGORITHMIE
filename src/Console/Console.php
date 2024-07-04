<?php

namespace App\EsgiAlgorithmie\Console;

use App\EsgiAlgorithmie\Enum\ConsoleEnum;

class Console
{
    public function read(ConsoleEnum $type, string $message, string $default = null): string|int|bool
    {
        if ($type == ConsoleEnum::Boolean) {
           return $this->returnBool(readLine($message));
        } else if ($type == ConsoleEnum::Integer) {
            return $this->returnInt(readLine($message));
        } else {
            return readline($message);
        }
    }

    public function message(string $message): void
    {
        echo "\033[32m$message\033[0m\n";
    }

    public function errorMessage(string $message): void
    {
        echo "\033[31m$message\033[0m\n";
    }

    public function displayArray(array $data): void
    {
        foreach ($data as $value) {
            echo "ID: {$data['id']}, Nom: {$data['name']}, Description: {$data['description']}, Disponible: " . ($data['is_available'] ? "Oui" : "Non") . "\n";
        }
    }

    private function returnBool(string $output): bool
    {
        if (strtolower($output) === 'yes') {
            return true;
        }

        return false;
    }

    private function returnInt(string $output): int
    {
        return intval($output);
    }

}