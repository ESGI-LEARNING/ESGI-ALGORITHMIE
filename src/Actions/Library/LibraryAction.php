<?php

namespace App\EsgiAlgorithmie\Actions\Library;

use App\EsgiAlgorithmie\Actions\FileStorage\FileStorageAction;
use App\EsgiAlgorithmie\Actions\Logs\LogAction;
use App\EsgiAlgorithmie\Model\Book;

class LibraryAction
{
    const FILE_NAME = 'livres.json';

    private array $books = [];

    private LogAction $logAction;

    public function __construct()
    {
        $this->books = FileStorageAction::getDataFile(self::FILE_NAME);
        $this->logAction = new LogAction();
    }

    /**
     * Create book
     *
     * @param string $name
     * @param string $description
     * @param bool $is_available
     * @return void
     */
    public function create(string $name, string $description, bool $is_available): void
    {
        $this->books[] = [
            'id' => $this->getLastId(),
            'name' => $name,
            'description' => $description,
            'is_available' => $is_available
        ];

        FileStorageAction::saveDataFile(self::FILE_NAME, $this->books);
        $this->logAction->add('Create book with id ' . $this->getLastId());
    }

    /**
     * Update book
     *
     * @param int $id
     * @param string $name
     * @param string $description
     * @param bool $is_available
     * @return void
     */
    public function update(int $id, string $name, string $description, bool $is_available): void
    {
        foreach ($this->books as $key => $book) {
            if ($book['id'] == $id) {
                $this->books[$key] = [
                    'id' => $id,
                    'name' => $name,
                    'description' => $description,
                    'is_available' => $is_available
                ];
                break;
            }
        }

        FileStorageAction::saveDataFile(self::FILE_NAME, $this->books);
        $this->logAction->add('Update book with id ' . $id);
    }

    /**
     * Delete book by id
     *
     * @param string $id
     * @return void
     */
    public function delete(string $id): void
    {
        for ($i = 0; $i < count($this->books); $i++) {
            if ($this->books[$i]['id'] == $id) {
                unset($this->books[$i]);
                break;
            }
        }

        FileStorageAction::saveDataFile(self::FILE_NAME, array_values($this->books));
        $this->logAction->add('Delete book with id ' . $id);
    }

    /**
     * Get all books
     *
     * @return array
     */
    public function getAll(): array
    {
        $this->logAction->add('Get All books');
        return $this->books;
    }

    /**
     * Get book by id
     *
     * @param string $id
     * @return array
     */
    public function get(string $id): array
    {
        // verify if id is integer
        $id = intval($id);

        foreach ($this->books as $book) {
            if ($book['id'] == $id) {
                $this->logAction->add('Get book with id ' . $id);
                return $book;
            }
        }

        return [];
    }

    /**
     * Get last id
     *
     * @return int
     */
    private function getLastId(): int
    {
        $id_max = 0;
        foreach ($this->books as $book) {
            if ($book['id'] > $id_max) {
                $id_max = $book['id'];
            }
        }

        return $id_max + 1;
    }
}