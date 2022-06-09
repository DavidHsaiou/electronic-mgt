<?php

namespace App\Utility;

use DateTime;
use DateTimeZone;

class TimeUtility {
    public static function toDisplyTime($datetime) {
        $checkTime = null;
        if (gettype($datetime) == 'string') {
            $checkTime = new DateTime($datetime);
        } else if (gettype($datetime) == 'DateTime') {
            $checkTime = $datetime;
        }

        if ($checkTime == null) return null;
        $checkTime->setTimezone(new DateTimeZone(config('app.timezone')));
        return $checkTime->format('Y-m-d H:i:s');
    }
}

