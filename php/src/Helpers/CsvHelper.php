<?php

namespace App\Php\Helpers;

use \Generator;
use \Exception;

class CsvHelper
{
    private static ?self $instance;

    public static function create(): self
    {
        return self::$instance ??= new static();
    }

    private function __construct()
    {
    }

    /**
     * @param string $path
     * @return Generator
     * @throws Exception
     */
    public function getLines(string $path): Generator
    {
        if($csv = fopen($path, "r")) {
            while (($line = fgetcsv($csv)) !== false) {
                yield $line;
            }
        } else {
            throw new Exception("Error: invalid path \"$path\"");
        }
    }
}
