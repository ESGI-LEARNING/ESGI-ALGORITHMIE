<?php

namespace App\EsgiAlgorithmie\Actions\FileStorage;

class FileStorageAction
{
    /**
     * Get file json with path
     *
     * @param string $path
     * @return mixed
     */
    public static function getDataFile(string $path): mixed
    {
        if (!file_exists($path)) {
            $file = fopen($path, 'w');
            fclose($file);
            $json = '[]';
        } else {
            $json = file_get_contents($path);
        }

        if (empty($json)) {
            return [];
        }

        return json_decode($json, true);
    }

    /**
     * Save data in file json with path
     *
     * @param string $path
     * @param array $data
     * @return void
     */
    public static function saveDataFile(string $path, array $data): void
    {
        file_put_contents($path, json_encode($data));
    }
}