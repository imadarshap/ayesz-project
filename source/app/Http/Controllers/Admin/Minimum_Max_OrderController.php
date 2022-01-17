<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Session;

class Minimum_Max_OrderController extends Controller
{
    public function amountupdate(Request $request)
    {
        $admin_email = Session::get('bamaAdmin');
		$admin = DB::table('admin')
			->where('admin_email', $admin_email)
			->first();
		if (!Helper::hasRight($admin->id, 'delivery_charges', 'Edit')) {
			return abort(403);
		}
        $title = "Home";
        $min_max_id = $request->min_max_id;
        $min_value = $request->min_value;
        $max_value = $request->max_value;

        $this->validate(
            $request,
            [

                'min_value' => 'required',
                'max_value' => 'required',
            ],
            [

                'min_value.required' => 'Min Value Required',
                'max_value.required' => 'Max Value Required',

            ]
        );

        $insert = DB::table('minimum_maximum_order_value')

            ->update([
                'min_value' => $min_value,
                'max_value' => $max_value,
            ]);

        return redirect()->back()->withSuccess('Updated Successfully');
    }


    public function orderedit(Request $request)
    {
        $title = "Delivery Charges";
        $admin_email = Session::get('bamaAdmin');
		$admin = DB::table('admin')
			->where('admin_email', $admin_email)
			->first();
		if (!Helper::hasRight($admin->id, 'delivery_charges', 'View')) {
			return abort(403);
		}
        $logo = DB::table('tbl_web_setting')
            ->where('set_id', '1')
            ->first();
        $min_max_id = $request->min_max_id;

        $categories = DB::table('categories')->get();

        $city = DB::table('delivery_charge')
            ->first();
        $charges = array();
        if ($request->cat_id) {
            $charges = DB::table('delivery_charge')->where('cat_id', $request->cat_id)->first();
        }


        return view('admin.order_amount.editorderamountnew', compact('title', 'city', 'logo', 'admin', 'categories', 'charges'));
    }

    public function amountupdatenew(Request $request)
    {
        $admin_email = Session::get('bamaAdmin');
		$admin = DB::table('admin')
			->where('admin_email', $admin_email)
			->first();
		if (!Helper::hasRight($admin->id, 'delivery_charges', 'Edit')) {
			return abort(403);
		}
        $title = "Home";
        $cat_id = $request->cat_id;
        $charge_type = $request->charge_type;
        $min = $request->min;
        $max = $request->max;
        $charge = $request->charge;

        $this->validate(
            $request,
            [

                // 'min_value'=>'required',
                // 'max_value'=>'required',
            ],
            [

                // 'min_value.required'=>'Min Value Required',
                // 'max_value.required'=>'Max Value Required',

            ]
        );
        $charges = DB::table('delivery_charge')->where('cat_id', $cat_id)->get();
        if (count($charges) == 0) {
            $insert = DB::table('delivery_charge')
                ->insert([
                    'cat_id' => $cat_id,
                    'charge_type' => $charge_type,
                    'charge' => implode('*', $charge),
                    'min' => implode('*', $min),
                    'max' => implode('*', $max)
                ]);
        } else {
            $insert = DB::table('delivery_charge')
                ->where('cat_id', $cat_id)
                ->update([
                    'charge_type' => $charge_type,
                    'charge' => implode('*', $charge),
                    'min' => implode('*', $min),
                    'max' => implode('*', $max)
                ]);
        }

        return redirect()->back()->withSuccess('Updated Successfully');
    }
}
