<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Session;

class CouponController extends Controller
{
    public function couponlist(Request $request)
    {
        $title = "Home";
        $admin_email = Session::get('bamaAdmin');
        $admin = DB::table('admin')
            ->where('admin_email', $admin_email)
            ->first();
        if (!Helper::hasRight($admin->id, 'coupons', 'View')) {
            return abort(403);
        }
        $logo = DB::table('tbl_web_setting')
            ->where('set_id', '1')
            ->first();
        $coupons = DB::table('coupon')
            ->get();
        return view('admin.coupon.couponlist', compact("title", "coupons", 'admin', 'logo'));
    }

    public function coupon(Request $request)
    {
        $title = "Home";
        $admin_email = Session::get('bamaAdmin');
        $admin = DB::table('admin')
            ->where('admin_email', $admin_email)
            ->first();
        if (!Helper::hasRight($admin->id, 'coupons', 'Add')) {
            return abort(403);
        }
        $logo = DB::table('tbl_web_setting')
            ->where('set_id', '1')
            ->first();

        $coupon = DB::table('coupon')
            ->get();
        return view('admin.coupon.couponadd', compact("title", "coupon", 'admin', 'logo'));
    }


    public function addcoupon(Request $request)
    {
        $admin_email = Session::get('bamaAdmin');
        $admin = DB::table('admin')
            ->where('admin_email', $admin_email)
            ->first();
        if (!Helper::hasRight($admin->id, 'coupons', 'Add')) {
            return abort(403);
        }
        $coupon_name = $request->coupon_name;
        $coupon_code = $request->coupon_code;
        $coupon_desc = $request->coupon_desc;
        $valid_to = $request->valid_to;
        $valid_from = $request->valid_from;
        $cart_value = $request->cart_value;
        $max_cart_value = $request->max_cart_value;
        $coupon_type = $request->coupon_type;
        $restriction = $request->restriction;
        $coupon_discount = $request->coupon_discount;
        $discount = str_replace("%", '', $coupon_discount);
        $max_discount = $request->max_discount;


        $this->validate(
            $request,
            [

                'coupon_name' => 'required',
                'coupon_code' => 'required',
                'coupon_desc' => 'required',
                'valid_to' => 'required',
                'valid_from' => 'required',
                'cart_value' => 'required',
                'restriction' => 'required'
            ],
            [

                'coupon_name.required' => 'Coupon Name Required',
                'coupon_code.required' => 'Coupon Code Required',
                'coupon_desc.required' => 'Coupon Description Required',
                'valid_to.required' => 'Date Required',
                'valid_from.required' => 'Date Required',
                'cart_value.required' => 'Cart value Required',
                'restriction.required' => 'Enter Uses Restiction limit'

            ]
        );


        $insert = DB::table('coupon')
            ->insert([
                'coupon_name' => $coupon_name,
                'coupon_description' => $coupon_desc,
                'coupon_code' => $coupon_code,
                'start_date' => $valid_to,
                'end_date' => $valid_from,
                'type' => $coupon_type,
                'uses_restriction' => $restriction,
                'amount' => $discount,
                'max_cart_value' => $max_cart_value,
                'cart_value' => $cart_value,
                'max_discount' => $max_discount
            ]);

        return redirect()->back()->withSuccess('Added Successfully');
    }

    public function editcoupon(Request $request)
    {
        $title = "Home";
        $admin_email = Session::get('bamaAdmin');
        $admin = DB::table('admin')
            ->where('admin_email', $admin_email)
            ->first();
        if (!Helper::hasRight($admin->id, 'coupons', 'Edit')) {
            return abort(403);
        }
        $logo = DB::table('tbl_web_setting')
            ->where('set_id', '1')
            ->first();
        $coupon_id = $request->coupon_id;
        $coupon = DB::table('coupon')
            ->where('coupon_id', $coupon_id)
            ->first();
        return view('admin.coupon.couponedit', compact("coupon", "coupon_id", "title", 'admin', 'logo'));
    }
    public function updatecoupon(Request $request)
    {
        $admin_email = Session::get('bamaAdmin');
        $admin = DB::table('admin')
            ->where('admin_email', $admin_email)
            ->first();
        if (!Helper::hasRight($admin->id, 'coupons', 'Edit')) {
            return abort(403);
        }
        $coupon_id = $request->coupon_id;
        $coupon_name = $request->coupon_name;
        $coupon_code = $request->coupon_code;
        $coupon_type = $request->coupon_type;
        $coupon_desc = $request->coupon_desc;
        $valid_to = $request->valid_to;
        $valid_from = $request->valid_from;
        $max_cart_value = $request->max_cart_value;
        $cart_value = $request->cart_value;
        $restriction = $request->restriction;
        $coupon_discount = $request->coupon_discount;
        $discount = str_replace("%", '', $coupon_discount);
        $max_discount = $request->max_discount;

        $this->validate(
            $request,
            [

                'coupon_name' => 'required',
                'coupon_code' => 'required',
                'coupon_desc' => 'required',
                'valid_to' => 'required',
                'valid_from' => 'required',
                'cart_value' => 'required',
                'restriction' => 'required'
            ],
            [

                'coupon_name.required' => 'Coupon Name Required',
                'coupon_code.required' => 'Coupon Code Required',
                'coupon_desc.required' => 'Coupon Description Required',
                'valid_to.required' => 'Date Required',
                'valid_from.required' => 'Date Required',
                'cart_value.required' => 'Cart value Required',
                'restriction.required' => 'Enter Uses Restiction limit'

            ]
        );
        $update = DB::table('coupon')
            ->where('coupon_id', $coupon_id)
            ->update([
                'coupon_name' => $coupon_name,
                'coupon_description' => $coupon_desc,
                'coupon_code' => $coupon_code,
                'start_date' => $valid_to,
                'type' => $coupon_type,
                'end_date' => $valid_from,
                'cart_value' => $cart_value,
                'amount' => $discount,
                'max_cart_value' => $max_cart_value,
                'uses_restriction' => $restriction,
                'max_discount' => $max_discount
            ]);

        if ($update) {

            return redirect()->back()->withSuccess(' Updated Successfully');
        } else {
            return redirect()->back()->withErrors("something wents wrong.");
        }
    }
    public function deletecoupon(Request $request)
    {
        $admin_email = Session::get('bamaAdmin');
        $admin = DB::table('admin')
            ->where('admin_email', $admin_email)
            ->first();
        if (!Helper::hasRight($admin->id, 'coupons', 'Edit')) {
            return abort(403);
        }

        $coupon_id = $request->coupon_id;

        $getfile = DB::table('coupon')
            ->where('coupon_id', $coupon_id)
            ->first();


        $delete = DB::table('coupon')->where('coupon_id', $request->coupon_id)->delete();
        if ($delete) {
            return redirect()->back()->withSuccess('Deleted Successfully');
        } else {
            return redirect()->back()->withErrors('Unsuccessfull Delete');
        }
    }
}
