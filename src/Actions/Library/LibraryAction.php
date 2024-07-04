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
        $book['id'] = $id;
        $book['name'] = $name;
        $book['description'] = $description;
        $book['is_available'] = $is_available;
        foreach ($this->books as &$objet) {
            if ($objet['id'] === $id) {
                $objet['name'] = $name;
                $objet['description'] = $description;
                $objet['is_available'] = $is_available;

                break;
            } else {
                $this->logAction->add('Book not found with id ' . $id);
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
        $books = $this->books;
        unset($books[$id]);

        FileStorageAction::saveDataFile(self::FILE_NAME, $books);
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
        return count($this->books) + 1;
    }
}