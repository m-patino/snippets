<?php

/**
 * yield is usefull for browse a csv file line by line. 
 * It's usefull because we don't load all csv content in memory.
 */
$getCsv = function(string $path): Generator{
    $csv = fopen($path,"r");
    while (($line = fgetcsv($csv)) !== false) {
        yield $line;
    }
};

foreach($getCsv('./test.csv') as $line){
    var_dump($line);
}