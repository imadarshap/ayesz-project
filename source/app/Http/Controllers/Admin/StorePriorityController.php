<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class StorePriorityController extends Controller
{
    public function store_priority(Request $request)
    {
        $title = "Home";
        $admin_email = Session::get('bamaAdmin');
        $admin = DB::table('admin')
            ->where('admin_email', $admin_email)
            ->first();
        if (!Helper::hasRight($admin->id, 'vendor_priority', 'View')) {
            return abort(403);
        }
        $logo = DB::table('tbl_web_setting')
            ->where('set_id', '1')
            ->first();
        

        $allowedCities = explode(',',$admin->locations);
        $cities = DB::table('city')->whereIn('city_name',$allowedCities)->get();

        $categories = DB::table('categories')->get();

        $stores = array();

        if (!empty($request->city) && !empty($request->cat_id)) {
            DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
            $stores = DB::table('store_categories')
                ->join('store', 'store.store_id', 'store_categories.store_id')
                ->where('cat_id', $request->cat_id)
                ->where('city', $request->city)
                // ->groupby('store.store_id')
                ->get();
        }

        return view('admin.store_priority.index', compact('title', 'cities', 'admin', 'logo', 'request', 'categories', 'stores'));
    }

    public function set_store_priority(Request $request)
    {
        $admin_email = Session::get('bamaAdmin');
        $admin = DB::table('admin')
            ->where('admin_email', $admin_email)
            ->first();
        if (!Helper::hasRight($admin->id, 'vendor_priority', 'Edit')) {
            return abort(403);
        }
        $store = DB::table('store_categories')->where('store_id', $request->store_id)->where('cat_id', $request->cat_id)->first();
        if (!empty($store)) {
            $update = DB::table('store_categories')->where('store_id', $request->store_id)->where('cat_id', $request->cat_id)->update(['store_priority' => $request->store_priority]);
            return json_encode(array('success' => true, 'message' => ''));
        } else {
            return json_encode(array('success' => false, 'message' => ''));
        }
    }

    public function get_stores_by_city(Request $request)
    {
        $admin_email = Session::get('bamaAdmin');
        $admin = DB::table('admin')
            ->where('admin_email', $admin_email)
            ->first();
        if (!Helper::hasRight($admin->id, 'vendors', 'View')) {
            return abort(403);
        }
        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
        $stores = DB::table('store_categories')
            ->join('store', 'store.store_id', 'store_categories.store_id')
            ->where('cat_id', $request->cat_id)
            ->where('city', $request->city)
            ->groupby('store.store_id')
            ->get();
        return array('stores' => $stores);
    }

    public function storeupdate(Request $request)
    {
        $admin_email = Session::get('bamaAdmin');
        $admin = DB::table('admin')
            ->where('admin_email', $admin_email)
            ->first();
        if (!Helper::hasRight($admin->id, 'vendor_priority', 'Edit')) {
            return abort(403);
        }
        $title = "Update store";
        $store_id = $request->store_id;
        $share = $request->share;
        $store_name = $request->store_name;
        $emp_name = $request->emp_name;
        $number = $request->number;
        $city = $request->city;
        $range = $request->range;
        $email = $request->email;
        $password = $request->password;
        $address = $request->address;
        $addres = str_replace(" ", "+", $address);
        $address1 = str_replace("-", "+", $addres);
        $checkmap = DB::table('map_api')
            ->first();
        $mapset = DB::table('map_settings')
            ->first();
        $chkstorphon = DB::table('store')
            ->where('phone_number', $number)
            ->where('store_id', '!=', $store_id)
            ->first();
        $chkstoremail = DB::table('store')
            ->where('email', $email)
            ->where('store_id', '!=', $store_id)
            ->first();


        if ($chkstorphon && $chkstoremail) {
            return redirect()->back()->withErrors('This Phone Number and Email Are Already Registered With Another Store');
        }

        if ($chkstorphon) {
            return redirect()->back()->withErrors('This Phone Number is Already Registered With Another Store');
        }
        if ($chkstoremail) {
            return redirect()->back()->withErrors('This Email is Already Registered With Another Store');
        }

        if ($mapset->mapbox == 0 && $mapset->google_map == 1) {
            $response = json_decode(file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=" . $address1 . "&key=" . $checkmap->map_api_key));

            $lat = $response->results[0]->geometry->location->lat;
            $lng = $response->results[0]->geometry->location->lng;
        } else {
            $lat = $request->lat;
            $lng = $request->lng;
        }
        $this->validate(
            $request,
            [

                'store_name' => 'required',
                'emp_name' => 'required',
                'number' => 'required',
                'email' => 'required',
                'password' => 'required',
                'range' => 'required',
                'address' => 'required',
                'share' => 'required'
            ],
            [

                'store_name.required' => 'Store Name Required',
                'emp_name.required' => 'Employee Name Required',
                'number.required' => 'Phone Number Required',
                'range.required' => 'Enter delivery range',
                'address.required' => 'Enter store address',
                'email.required' => 'E-mail Address Required',
                'password.required' => 'Password Required',
                'share.required' => 'Admin Share Required'

            ]
        );

        $insert = DB::table('store')
            ->where('store_id', $store_id)
            ->update([
                'store_name' => $store_name,
                'employee_name' => $emp_name,
                'phone_number' => $number,
                'city' => $city,
                'email' => $email,
                'del_range' => $range,
                'password' => $password,
                'address' => $address,
                'lat' => $lat,
                'lng' => $lng,
                'admin_share' => $share,
            ]);

        return redirect()->back()->withSuccess('Updated Successfully');
    }
}
