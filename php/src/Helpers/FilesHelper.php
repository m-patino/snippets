<?php

namespace App\Php\Helpers;

class FilesHelper
{
    /**
     * @param string $pattern
     * @param int $flag
     * @return array
     */
    public static function recursiveGlob(string $pattern, int $flag = 0):array
    {
        $files = [];
        foreach (glob($pattern, GLOB_ONLYDIR) as $dir){
            array_push($files, ...self::recursiveGlob("$dir/*", $flag));
        }

        array_push($files, ...glob($pattern, $flag));
        return $files;
    }
}