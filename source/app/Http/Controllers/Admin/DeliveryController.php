<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Session;

class DeliveryController extends Controller
{
    public function list(Request $request)
    {
        $title = "Delivery Agent List";
        $admin_email = Session::get('bamaAdmin');
        $admin = DB::table('admin')
            ->where('admin_email', $admin_email)
            ->first();
        if (!Helper::hasRight($admin->id, 'delivery_agent', 'View')) {
            return abort(403);
        }
        $logo = DB::table('tbl_web_setting')
            ->where('set_id', '1')
            ->first();
        //  $d_boy = DB::table('delivery_boy')
        $d_boy = DB::table('delivery_boy')->orderby('delivery_boy.status', 'DESC')->get();
        //   ->get();


        return view('admin.d_boy.list', compact('title', "admin", "logo", "d_boy"));
    }


    public function AddD_boy(Request $request)
    {

        $title = "Add Delivery Agent";
        $admin_email = Session::get('bamaAdmin');
        $admin = DB::table('admin')
            ->where('admin_email', $admin_email)
            ->first();
        if (!Helper::hasRight($admin->id, 'delivery_agent', 'Add')) {
            return abort(403);
        }
        $logo = DB::table('tbl_web_setting')
            ->where('set_id', '1')
            ->first();
        $d_boy = DB::table('delivery_boy')
            ->get();
        $city = DB::table('city')
            ->get();

        $map1 = DB::table('map_api')
            ->first();
        $map = $map1->map_api_key;
        $mapset = DB::table('map_settings')
            ->first();
        $mapbox = DB::table('mapbox')
            ->first();

        return view('admin.d_boy.add', compact("d_boy", "admin_email", "logo", "admin", "title", 'city', 'map', 'mapset', 'mapbox'));
    }

    public function AddNewD_boy(Request $request)
    {
        $admin_email = Session::get('bamaAdmin');
        $admin = DB::table('admin')
            ->where('admin_email', $admin_email)
            ->first();
        if (!Helper::hasRight($admin->id, 'delivery_agent', 'Add')) {
            return abort(403);
        }
        $boy_name = $request->boy_name;
        $boy_phone = $request->boy_phone;
        $password = $request->password;
        $boy_loc = $request->boy_loc;
        $city = $request->city;
        $status = $request->status;
        $date = date('d-m-Y');

        $addres = str_replace(" ", "+", $boy_loc);
        $address1 = str_replace("-", "+", $addres);
        $mapapi = DB::table('map_api')
            ->first();
        $mapset = DB::table('map_settings')
            ->first();

        $chkboyrphon = DB::table('delivery_boy')
            ->where('boy_phone', $boy_phone)
            ->first();

        if ($chkboyrphon) {
            return redirect()->back()->withErrors('This Phone Number Is Already Registered With Another Delivery Agent');
        }

        $key = $mapapi->map_api_key;
        if ($mapset->mapbox == 0 && $mapset->google_map == 1) {
            $response = json_decode(file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=" . $address1 . "&key=" . $key));


            $lat = $response->results[0]->geometry->location->lat;
            $lng = $response->results[0]->geometry->location->lng;
        } else {
            $lat = $request->lat;
            $lng = $request->lng;
        }

        $this->validate(
            $request,
            [

                'boy_name' => 'required',
                'boy_phone' => 'required',
                'password' => 'required',
                'boy_loc' => 'required',
                'city' => 'required',

            ],
            [
                'boy_name.required' => 'Enter Agent Name.',
                'boy_phone.required' => 'Choose Agent Phone.',
                'password.required' => 'choose password',
                'boy_loc.required' => 'enter boy location',
                'city.required' => 'enter boy city',
            ]
        );


        $insert = DB::table('delivery_boy')
            ->insert([
                'boy_name' => $boy_name,
                'boy_phone' => $boy_phone,
                'boy_city' => $city,
                'password' => $password,
                'boy_loc' => $boy_loc,
                'lat' => $lat,
                'lng' => $lng,
                'status' => $status,

            ]);

        if ($insert) {
            return redirect()->back()->withSuccess('Delivery Agent Added Successfully');
        } else {
            return redirect()->back()->withErrors("Something Wents Wrong");
        }
    }

    public function EditD_boy(Request $request)
    {

        $dboy_id = $request->id;
        $title = "Edit Delivery Agent";
        $admin_email = Session::get('bamaAdmin');
        $admin = DB::table('admin')
            ->where('admin_email', $admin_email)
            ->first();
        if (!Helper::hasRight($admin->id, 'delivery_agent', 'Edit')) {
            return abort(403);
        }
        $logo = DB::table('tbl_web_setting')
            ->where('set_id', '1')
            ->first();

        $d_boy =  DB::table('delivery_boy')
            ->where('dboy_id', $dboy_id)
            ->first();
        $city = DB::table('city')
            ->get();

        $map1 = DB::table('map_api')
            ->first();
        $map = $map1->map_api_key;
        $mapset = DB::table('map_settings')
            ->first();
        $mapbox = DB::table('mapbox')
            ->first();
        return view('admin.d_boy.edit', compact("d_boy", "admin_email", "admin", "logo", "title", "city", "map", 'mapset', 'mapbox'));
    }

    public function UpdateD_boy(Request $request)
    {
        $admin_email = Session::get('bamaAdmin');
        $admin = DB::table('admin')
            ->where('admin_email', $admin_email)
            ->first();
        if (!Helper::hasRight($admin->id, 'delivery_agent', 'Edit')) {
            return abort(403);
        }
        $dboy_id = $request->id;
        $boy_name = $request->boy_name;
        $boy_phone = $request->boy_phone;
        $password = $request->password;
        $boy_loc = $request->boy_loc;
        $city = $request->city;
        $status = $request->status;
        $mapset = DB::table('map_settings')
            ->first();
        $addres = str_replace(" ", "+", $boy_loc);
        $address1 = str_replace("-", "+", $addres);

        $chkboyrphon = DB::table('delivery_boy')
            ->where('boy_phone', $boy_phone)
            ->where('dboy_id', '!=', $dboy_id)
            ->first();

        if ($chkboyrphon) {
            return redirect()->back()->withErrors('This Phone Number Is Already Registered With Another Delivery Agent');
        }

        $mapapi = DB::table('map_api')
            ->first();

        $key = $mapapi->map_api_key;
        if ($mapset->mapbox == 0 && $mapset->google_map == 1) {
            $response = json_decode(file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=" . $address1 . "&key=" . $key));

            $lat = $response->results[0]->geometry->location->lat;
            $lng = $response->results[0]->geometry->location->lng;
        } else {
            $lat = $request->lat;
            $lng = $request->lng;
        }

        $this->validate(
            $request,
            [

                'boy_name' => 'required',
                'boy_phone' => 'required',
                'password' => 'required',
                'boy_loc' => 'required',
                'city' => 'required',
                'status' => 'required'
            ],
            [
                'boy_name.required' => 'Enter Agent Name.',
                'boy_phone.required' => 'Choose Agent Phone.',
                'password.required' => 'Choose password',
                'boy_loc.required' => 'Enter boy location',
                'city.required' => 'Enter boy city',
                'status.required' => 'Select status'
            ]
        );


        $updated = DB::table('delivery_boy')
            ->where('dboy_id', $dboy_id)
            ->update([
                'boy_name' => $boy_name,
                'boy_phone' => $boy_phone,
                'boy_city' => $city,
                'password' => $password,
                'boy_loc' => $boy_loc,
                'lat' => $lat,
                'lng' => $lng,
                'status' => $status

            ]);

        if ($updated) {
            return redirect()->back()->withSuccess('Delivery Agent Updated Successfully');
        } else {
            return redirect()->back()->withErrors("Something Wents Wrong");
        }
    }

    public function DeleteD_boy(Request $request)
    {
        $admin_email = Session::get('bamaAdmin');
        $admin = DB::table('admin')
            ->where('admin_email', $admin_email)
            ->first();
        if (!Helper::hasRight($admin->id, 'delivery_agent', 'Delete')) {
            return abort(403);
        }
        $dboy_id = $request->id;

        $delete = DB::table('delivery_boy')
            ->where('dboy_id', $dboy_id)->delete();
        if ($delete) {
            return redirect()->back()->withSuccess('Deleted Successfully');
        } else {
            return redirect()->back()->withErrors('Unsuccessfull Delete');
        }
    }
}
