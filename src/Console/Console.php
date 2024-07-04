<?php

namespace App\EsgiAlgorithmie\Console;

use DataType;

class Console
{
    public function read(\DataType $type, string $message, string $default = null): string|int|bool
    {
        if ($type == DataType::Boolean) {
           return $this->returnBool(readLine($message));
        } else if ($type == DataType::Integer) {
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