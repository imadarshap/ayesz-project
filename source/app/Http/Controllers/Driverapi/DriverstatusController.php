<?php

namespace App\Http\Controllers\Driverapi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;

class DriverstatusController extends Controller
{
   public function status(Request $request)
    { 
        $dboy_id = $request->dboy_id; 
        $status = $request->status;
        $check = $request->check;
        
        if($check=='true'){
            $checkStatus = DB::table('delivery_boy')->where('dboy_id', $dboy_id)->select('status')->first();
            if($checkStatus){
                $message = array('status'=>'1','onduty'=>$checkStatus->status, 'message'=>'Status Updated');
        	    return $message;
            }
            else{
                $message = array('status'=>'0', 'message'=>'Nothing happened');
            	return $message;
            }
        }else{
            $update= DB::table('delivery_boy') 
               ->where('dboy_id', $dboy_id)
               ->update(['status'=>$status]);
            if($update){
                $message = array('status'=>'1', 'message'=>'Status Updated');
        	    return $message;
            }
            else{
                $message = array('status'=>'0', 'message'=>'Status Not Updated');
            	return $message;
            }
        } 
        
               
    }
}