<?php
namespace App\Helper;

use DB;
use Session;
class Helper
{
    public static function getFullDay($day){
        $days = ['mon','tue','wed','thu','fri','sat','sun'];
        $day_names = ['Monday','Tuesday','Wedneuday','Thursday','Friday','Saturday','Sunday'];
        return $day_names[array_search($day,$days)];
    }
}

?>