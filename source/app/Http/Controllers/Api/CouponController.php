<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CouponController extends Controller
{
    public function apply_coupon(Request $request)
    {
        $currentdate = Carbon::now();
        $coupon_code = $request->coupon_code;
        $user_id = $request->user_id;
        $items = $request->cart_items;
        $p = $request->cart_value;

        $coupon = DB::table('coupon')
            ->where('coupon_code', $coupon_code)
            ->where('cart_value', '<=', $p)
            ->where('start_date', '<=', $currentdate)
            ->where('end_date', '>=', $currentdate)
            ->first();
        if(!$coupon){
            $message = array('status' => '0', 'message' => 'Invalid Coupon!');
            return $message;
        }

        $check2 = DB::table('orders')
            ->where('coupon_id', $coupon->coupon_id)
            ->where('user_id', $user_id)
            ->where('payment_method','!=',NULL)
            ->whereIn('order_status',['Pending','Confirmed','Accepted_By_Delivery_Agent','Out_For_Delivery','Completed'])
            ->count();

        if ($coupon->uses_restriction > $check2) {

            $mincart = $coupon->cart_value;
            $am = $coupon->amount;
            $type = $coupon->type;
            if ($type == '%' || $type == 'Percentage' || $type == 'percentage') {
                $per = ($p * $am) / 100;
                if($coupon->max_discount != null && $coupon->max_discount < $per){
                    $per = $coupon->max_discount;
                }
                $rem_price = $p - $per;
            } else {
                $per = $am;
                $rem_price = $p - $am;
            }

            $data = array(
                'rem_price' => $rem_price,
                'coupon_discount' => $per,
                'coupon_id' => $coupon->coupon_id
            );

            $message = array('status' => '1', 'message' => 'Coupon Applied Successfully','data'=>$data);
            return $message;
        } else {
            $message = array('status' => '0', 'message' => 'Invalid Coupon! Maximum use limit reached');
            return $message;
        }
    }

    public function coupon_list(Request $request)
    {
        $currentdate = Carbon::now();
        $p = $request->cart_value;

        $coupon = DB::table('coupon')
            ->where('cart_value', '<=', $p)
            ->where('start_date', '<=', $currentdate)
            ->where('end_date', '>=', $currentdate)
            ->get();
        if (count($coupon) > 0) {
            $message = array('status' => '1', 'message' => 'Coupon List', 'data' => $coupon);
            return $message;
        } else {
            $message = array('status' => '0', 'message' => 'Coupon not Found');
            return $message;
        }
    }
}
