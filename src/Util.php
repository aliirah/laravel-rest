<?php

namespace alirah\laravelRest;

use Illuminate\Support\Str;

class Util
{
    public function transformInput(string $arg): string
    {
        $dashToBackSlash = str_replace("/", "\\", $arg);
        $modelEl = explode("\\", $dashToBackSlash);
        $upperEl = [];
        foreach ($modelEl as $element) {
            $upperEl[] = Str::ucfirst(Str::camel($element));
        }
        return implode("\\", $upperEl);
    }

    /**
     * @param $str
     * @param string $separator
     * @return string
     */
    public function camelCase2UnderScore($str, string $separator = "_"): string
    {
        if (empty($str)) return $str;

        $str = lcfirst($str);
        $str = preg_replace("/[A-Z]/", $separator . "$0", $str);
        return strtolower($str);
    }

    public function matchInFile(string $path, string $string): bool|int|null
    {
        // get the file contents, assuming the file to be readable (and exist)
        $contents = file_get_contents($path);
        // escape special characters in the query
        $pattern = preg_quote($string, '/');
        $pattern = "/^.*$pattern.*\$/m";

        return preg_match_all($pattern, $contents);
    }
}
