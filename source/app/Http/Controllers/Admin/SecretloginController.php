<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Session;

class SecretloginController extends Controller
{
    public function secretlogin(Request $request)
    {
        $admin_email = Session::get('bamaAdmin');
        $admin = DB::table('admin')
            ->where('admin_email', $admin_email)
            ->first();
        if(!Helper::hasRight($admin->id,'vendor_login','View')){
            return abort(403);
        }
        $id=$request->id;
        $checkstoreLogin = DB::table('store')
    	                   ->where('store_id',$id)
    	                   ->first();

    	if($checkstoreLogin){

           session::put('bamaStore',$checkstoreLogin->email);
           return redirect()->route('storeHome');
         
    	}else
         {
         	 return redirect()->back()->withErrors('Something Wents Wrong');
         }
    }
}