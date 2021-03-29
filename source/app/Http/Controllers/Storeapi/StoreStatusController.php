<?php

namespace App\Http\Controllers\Storeapi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;

class StoreStatusController extends Controller
{
   public function update_status(Request $request)
    { 
        $store_id = $request->store_id; 
        $status = $request->status;
        $check = $request->check;
        
        if($check=='true'){
            $checkStatus = DB::table('store')->where('store_id', $store_id)->select('availability')->first();
            if($checkStatus){
                $message = array('status'=>'1','available'=>$checkStatus->availability, 'message'=>'Status Updated');
        	    return $message;
            }
            else{
                $message = array('status'=>'0', 'message'=>'Nothing happened');
            	return $message;
            }
        }else{
            $update= DB::table('store') 
               ->where('store_id', $store_id)
               ->update(['availability'=>$status]);
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