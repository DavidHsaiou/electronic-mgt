<?php

namespace App\Utility;

class NumberUtility
{
    public static function toDisplyFloat($number)
    {
        return round($number, 2);
    }
}
