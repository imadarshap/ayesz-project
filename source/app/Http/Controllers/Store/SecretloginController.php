<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Session;

class SecretloginController extends Controller
{
    public function secretlogin(Request $request)
    {
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
    
    public function vendorSettings(Request $request)
    {
        $email = $request->email;
        $password = $request->password;
        $checkstoreLogin = DB::table('store')
    	                   ->where('email',$email)
    	                   ->where('password',$password)
    	                   ->first();
    	if($checkstoreLogin){
           session::put('bamaStore',$checkstoreLogin->email);
           session::put('bamaStoreMobile',true);
           echo "No";
        //   return redirect()->route('settings');
    	}else{
    	    echo "yes";
    	}
    }
    
    
    
}