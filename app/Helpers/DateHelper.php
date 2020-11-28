<?php
/**
 * Author: Sebastian Rogala
 * Mail: sebrogala@gmail.com
 * Created: 28.02.2019
 */

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    public function isThatDateWorkingDay($date) {
        //source: https://lukasz-socha.pl/php/skrypt-do-sprawdzania-dni-roboczych/
        $time = strtotime($date);
        $dayOfWeek = (int)date('w',$time);
        $year = (int)date('Y',$time);

        if( $dayOfWeek==6 || $dayOfWeek==0 ) {
            return false;
        }

        $holiday=array('01-01', '01-06','05-01','05-03','08-15','11-01','11-11','12-25','12-26');

        $easter = date('m-d', easter_date( $year ));
        $easterSec = date('m-d', strtotime('+1 day', strtotime( $year . '-' . $easter) ));
        $cc = date('m-d', strtotime('+60 days', strtotime( $year . '-' . $easter) ));
        $p = date('m-d', strtotime('+49 days', strtotime( $year . '-' . $easter) ));

        $holiday[] = $easter;
        $holiday[] = $easterSec;
        $holiday[] = $cc;
        $holiday[] = $p;

        $md = date('m-d',strtotime($date));
        if(in_array($md, $holiday)) return false;

        return true;
    }

    public function nearestWorkingDay(Carbon $date)
    {
        $time = strtotime($date);
        $dayOfWeek = (int)date('w', $time);

        if($dayOfWeek == 6){
            $daysToAdd = 2;
        } else {
            $daysToAdd = 1;
        }
        while (true) {
            $isWorkingDay = $date->addDay($daysToAdd);

            if ($this->isThatDateWorkingDay($isWorkingDay)) {
                return $isWorkingDay->format("Y-m-d");
            }

            $daysToAdd++;
        }
    }

    public function isTimeBetween($a, $b, Carbon $date = null)
    {
        if ($date === null) {
            $date = new Carbon();
        }

        return $date->hour >= $a && $date->hour < $b;
    }

    public static function dateRangeOrDate(string $from, string $to): string
    {
        if($from === $to) {
            $date = ' z dnia ' . $from;
        } else {
            $date = ' od dnia ' . $from . ' do dnia ' . $to;
        }

        return $date;
    }
}
