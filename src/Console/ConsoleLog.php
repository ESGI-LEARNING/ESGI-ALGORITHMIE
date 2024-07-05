<?php

namespace App\EsgiAlgorithmie\Console;

use App\EsgiAlgorithmie\Actions\Logs\LogAction;

class ConsoleLog
{
    public static function display(): void
    {
        $log = new LogAction();
        $data = $log->read();

        foreach ($data as $value) {
            echo "{$value}";
        }
    }
}