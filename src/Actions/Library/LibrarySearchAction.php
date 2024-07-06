<?php

namespace App\EsgiAlgorithmie\Actions\Library;

use App\EsgiAlgorithmie\Actions\FileStorage\FileStorageAction;
use App\EsgiAlgorithmie\Actions\Logs\LogAction;

class LibrarySearchAction
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
     * @param string $col
     * @param string $order
     * @return void
     */
    public function sortBooks(string $col, string $order = "asc"): void
    {
        $books = $this->books;
        $this->sortFusion($books, $col, $order);
        $this->books = array_combine(array_column($books, 'id'), $books);

        FileStorageAction::saveDataFile(self::FILE_NAME, array_values($this->books));
        $this->logAction->add("Sort book success with '$col' ($order)");
    }


    /**
     * Search for a book in the library using quick sort and binary search
     *
     * @param string $col
     * @param string $value
     * @return array
     */
    public function searchBook(string $col, string $value): array
    {
        $books = $this->books;
        $this->fastSort($books, 0, count($books) - 1, $col);

        $left = 0;
        $right = count($books) - 1;

        while ($left <= $right) {
            $middle = floor(($left + $right) / 2);
            $compare = strcmp($books[$middle][$col], $value);

            if ($compare == 0) {
                $this->logAction->add("Search success with col $col and value $value");
                return $books[$middle];
            }

            if ($compare < 0) {
                $left = $middle + 1;
            } else {
                $right = $middle - 1;
            }
        }

        $this->logAction->add("Search not fount with col $col and value $value");
        return [];
    }


    /**
     * Implementation sort fusion
     *
     * @param array $books
     * @param string $col
     * @param string $order
     * @return void
     */
    private function sortFusion(array &$books, string $col, string $order): void
    {
        if (count($books) <= 1) {
            return;
        }

        $middle = floor(count($books) / 2);
        $left = array_slice($books, 0, $middle);
        $right = array_slice($books, $middle);

        $this->sortFusion($left, $col, $order);
        $this->sortFusion($right, $col, $order);

        $this->merge($books, $left, $right, $col, $order);
    }


    /**
     * Merges two sorted sub-tables
     *
     * @param array $books
     * @param array $left
     * @param array $right
     * @param string $col
     * @param string $order
     * @return void
     */
    private function merge(array &$books, array $left, array $right, string $col, string $order): void
    {
        $i = 0;
        $j = 0;
        $k = 0;

        while ($i < count($left) && $j < count($right)) {
            $compare = $this->compareBook($left[$i], $right[$j], $col);

            if (($order === "asc" && $compare <= 0) || ($order === "desc" && $compare > 0)) {
                $books[$k] = $left[$i];
                $i++;
            } else {
                $books[$k] = $right[$j];
                $j++;
            }

            $k++;
        }

        while ($i < count($left)) {
            $books[$k] = $left[$i];
            $i++;
            $k++;
        }

        while ($j < count($right)) {
            $books[$k] = $right[$j];
            $j++;
            $k++;
        }
    }


    /**
     * Compare two books according to a given column
     *
     * @param array $a
     * @param array $b
     * @param string $col
     * @return int
     */
    private function compareBook(array $a, array $b, string $col): int
    {
        return strcmp($a[$col], $b[$col]);
    }


    /**
     * Implementing quick sorting
     *
     * @param array $books
     * @param int $start
     * @param int $end
     * @param string $col
     * @return void
     */
    private function fastSort(array &$books, int $start, int $end, string $col): void
    {
        if ($start < $end) {
            $pivot = $this->partition($books, $start, $end, $col);
            $this->fastSort($books, $start, $pivot - 1, $col);
            $this->fastSort($books, $pivot + 1, $end, $col);
        }
    }


    /**
     * Partition the table for quick sorting
     *
     * @param array $books
     * @param int $start
     * @param int $end
     * @param string $col
     * @return int
     */
    private function partition(array &$books, int $start, int $end, string $col): int
    {
        $pivot = $books[$end];
        $i = $start - 1;

        for ($j = $start; $j < $end; $j++) {
            if ($this->compareBook($books[$j], $pivot, $col) <= 0) {
                $i++;
                $temp = $books[$i];
                $books[$i] = $books[$j];
                $books[$j] = $temp;
            }
        }

        $temp = $books[$i + 1];
        $books[$i + 1] = $books[$end];
        $books[$end] = $temp;

        return $i + 1;
    }
}