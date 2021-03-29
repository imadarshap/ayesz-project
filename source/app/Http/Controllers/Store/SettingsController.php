<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Session;

class SettingsController extends Controller
{
    public function settings(Request $request){
        $title = "Availability Settings";
        $email=Session::get('bamaStore');
    	$store= DB::table('store')
    	 		   ->where('email',$email)
    	 		   ->first();
    	if(empty($store)){
        	return redirect('store/logout');
        }
        $avails = DB::table('vendor_availability')->where('store_id',$store->store_id)->get();
        $logo = DB::table('tbl_web_setting')
                ->where('set_id', '1')
                ->first();
                
        $mobile=Session::get('bamaStoreMobile');
        
        return view('store.settings.settings', compact('title',"store", "logo",'avails','mobile'));
    }
    public function set_availability(Request $request){
        $email=Session::get('bamaStore');
    	$store= DB::table('store')
    	 		   ->where('email',$email)
    	 		   ->first();
    	if(empty($store)){
        	return redirect('store/logout');
        }
        $days = ['mon','tue','wed','thu','fri','sat','sun'];
        foreach($days as $day){
            if($request->has($day.'_status')){
                $start_time = implode(',',$request->get($day.'_start'));
                $end_time = implode(',',$request->get($day.'_end'));
                
                $check = DB::table('vendor_availability')->where('store_id',$store->store_id)->where('day',$day)->get();
                if(count($check)>0){
                    $update = DB::table('vendor_availability')->where('store_id',$store->store_id)->where('day',$day)->update(['start_time'=>$start_time,'end_time'=>$end_time,'status'=>1]);
                }else{
                    $insert = DB::table('vendor_availability')->insert(['start_time'=>$start_time,'end_time'=>$end_time,'status'=>1,'store_id'=>$store->store_id,'day'=>$day]);
                }
            }else{
                $check = DB::table('vendor_availability')->where('store_id',$store->store_id)->where('day',$day)->get();
                if(count($check)>0){
                    $update = DB::table('vendor_availability')->where('store_id',$store->store_id)->where('day',$day)->update(['status'=>0]);
                }else{
                    $insert = DB::table('vendor_availability')->insert(['start_time'=>'','end_time'=>'','status'=>0,'store_id'=>$store->store_id,'day'=>$day]);
                }
            }
        }
        return redirect()->back()->withSuccess('Availability Updated Successfully');
    }
    public function change_status(Request $request){
        $email=Session::get('bamaStore');
    	$store= DB::table('store')
    	 		   ->where('email',$email)
    	 		   ->first();
    	if(empty($store)){
        	return redirect('store/logout');
        }
        if($request->has('status'))
            $status = 1;
        else
            $status = 0;
        $update = DB::table('store')->where('store_id',$store->store_id)->update(['availability'=>$status]);
        return redirect()->back()->withSuccess('Status Updated Successfully');
    }
}