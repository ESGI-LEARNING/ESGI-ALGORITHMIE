<?php

namespace App\EsgiAlgorithmie\Actions\Logs;

class LogAction
{
    /**
     * Add log in file text
     *
     * @param string $message
     * @return void
     */
    public static function add(string $message): void
    {
        $file = fopen(__DIR__ . '/logs/logs.txt', 'a+');
        fwrite($file, $message . 'at' . date('Y-m-d H:i:s') . PHP_EOL);
        fclose($file);
    }
}