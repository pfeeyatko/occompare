<?php

namespace App\Helpers;

class OccompareHelper
{
    public static function compareBySkillName($a, $b) 
    {
        return strcmp($a[1], $b[1]);
    }

    public static function compareByImportance($a, $b) 
    {
        return (int) $a[0] - (int) $b[0];
    }

    public static function compareSimilarity($number1, $number2) 
    {
        return (1 - abs($number1 - $number2) / max($number1, $number2)) * 100;
    }
}