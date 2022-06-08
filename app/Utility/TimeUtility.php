<?php

namespace App\Utility;

use DateTime;

class TimeUtility {
    public static function toDisplyTime($datetime) {
        $checkTime = null;
        if (gettype($datetime) == 'string') {
            $checkTime = new DateTime($datetime);
        } else if (gettype($datetime) == 'DateTime') {
            $checkTime = $datetime;
        }

        return $checkTime->format('Y-m-d H:m:s');
    }
}

