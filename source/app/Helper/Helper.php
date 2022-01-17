<?php
namespace App\Helper;

use App\UserRight;
use DB;
use Session;
class Helper
{
    public static function getFullDay($day){
        $days = ['mon','tue','wed','thu','fri','sat','sun'];
        $day_names = ['Monday','Tuesday','Wedneuday','Thursday','Friday','Saturday','Sunday'];
        return $day_names[array_search($day,$days)];
    }

    public static function getModules(){
        return  [
            ["title"=>"Dashboard","module"=>"dashboard","rights"=>"View"],
            ["title"=>"Orders","module"=>"orders","rights"=>"View,Edit"],
            ["title"=>"Categories","module"=>"categories","rights"=>"View,Add,Edit,Delete"],
            ["title"=>"Products","module"=>"products","rights"=>"View,Add,Edit,Delete"],
            ["title"=>"Varients","module"=>"varients","rights"=>"View,Add,Edit,Delete"],
            ["title"=>"Vendors","module"=>"vendors","rights"=>"View,Add,Edit,Delete"],
            ["title"=>"Vendor Login","module"=>"vendor_login","rights"=>"View"],
            ["title"=>"Vendor Earnings/Payments","module"=>"vendor_earnings","rights"=>"View,Add"],
            ["title"=>"Vendor Aprroval List","module"=>"vendor_aprroval_list","rights"=>"View,Edit"],
            ["title"=>"Vendor Priority","module"=>"vendor_priority","rights"=>"View,Edit"],
            ["title"=>"Customer","module"=>"customers","rights"=>"View,Edit,Delete"],
            ["title"=>"Delivery Agent","module"=>"delivery_agent","rights"=>"View,Add,Edit,Delete"],
            ["title"=>"Reports","module"=>"reports","rights"=>"View"],
            ["title"=>"Cities","module"=>"cities","rights"=>"View,Add,Edit,Delete"],
            ["title"=>"Payout Request/Validation","module"=>"payout","rights"=>"View,Edit"],
            ["title"=>"Delivery Charges","module"=>"delivery_charges","rights"=>"View,Edit"],
            ["title"=>"Send Notification","module"=>"notifications","rights"=>"View"],
            ["title"=>"Admin Management","module"=>"admin_users","rights"=>"View,Add,Edit,Delete"],
            ["title"=>"Settings","module"=>"settings","rights"=>"View,Edit"],
            ["title"=>"Reward","module"=>"reward","rights"=>"View,Add,Edit,Delete"],
            ["title"=>"Redeem","module"=>"redeem","rights"=>"View,Edit"],
            ["title"=>"Banners","module"=>"banners","rights"=>"View,Add,Edit,Delete"],
            ["title"=>"Coupons","module"=>"coupons","rights"=>"View,Add,Edit,Delete"],
            ["title"=>"Pages","module"=>"pages","rights"=>"Edit"],
        ];
    }

    public static function hasRight($id,$module,$right){
        $result = UserRight::where('admin_id',$id)->where('module',$module)->select('rights')->first();
        if(!empty($result) && in_array($right, explode(',',$result->rights)))
            return true;
        else
            return false;
    }
}

?>