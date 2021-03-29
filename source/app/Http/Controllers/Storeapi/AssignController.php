<?php

namespace App\Http\Controllers\Storeapi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;

class AssignController extends Controller
{
    
 public function confirm_order(Request $request){
     $cart_id= $request->cart_id;
     $store_id = $request->store_id;
     $store= DB::table('store')
	 		   ->where('store_id',$store_id)
	 		   ->first();
	 		   
	 $nearbydboy = DB::table('delivery_boy')
            ->leftJoin('orders', 'delivery_boy.dboy_id', '=', 'orders.dboy_id')
            ->select("delivery_boy.boy_name","delivery_boy.dboy_id","delivery_boy.lat","delivery_boy.lng","delivery_boy.boy_city",DB::raw("Count(orders.order_id)as count"),DB::raw("6371 * acos(cos(radians(".$store->lat . ")) 
            * cos(radians(delivery_boy.lat)) 
            * cos(radians(delivery_boy.lng) - radians(" . $store->lng . ")) 
            + sin(radians(" .$store->lat. ")) 
            * sin(radians(delivery_boy.lat))) AS distance"))
           ->groupBy("delivery_boy.boy_name","delivery_boy.dboy_id","delivery_boy.lat","delivery_boy.lng","delivery_boy.boy_city")
           ->where('delivery_boy.boy_city', $store->city)
           ->where('delivery_boy.status',1)
           ->orderBy('distance')
           ->get();
          
    if (count($nearbydboy)>0){
        if(count($nearbydboy)==1){
            return $this->assignOrder($store_id,$cart_id,$nearbydboy[0]->dboy_id);
        }else{
            $assigned = false;
            $dboy_id = $nearbydboy[0]->dboy_id;
            foreach($nearbydboy as $dboy){
                $orders = DB::table('orders')
                    ->where('dboy_id',$dboy->dboy_id)
                    ->where('order_status','!=','Completed')
                    ->where('order_status','!=','Cancelled')
                    ->where('order_status','!=','Rejected_By_Delivery_Agent')
                    ->where('order_status','!=','Rejected_By_Vendor')
                    ->where('order_date',date('Y-m-d'))
                    ->get();
                if(count($orders)==0){
                    $dboy_id = $dboy->dboy_id;
                    break;
                }
            }
            return $this->assignOrder($store_id,$cart_id,$dboy_id);
        }
        $message = array('status'=>'1', 'message'=>'Delivery Boy List', 'data'=>$nearbydboy);
        return $message;
    }
	else{
		$message = array('status'=>'0', 'message'=>'No Delivery Boy In Your City');
        return $message;
	}
 }
 
 public function assignOrder($store_id,$cart_id,$dboyid){
     $curr = DB::table('currency')
             ->first();
       
        $store= DB::table('store')
        	->where('store_id',$store_id)
    	 	->first();
       
        $getDevice = DB::table('delivery_boy')
             ->where('dboy_id', $dboyid)
            ->select('device_id','boy_name')
            ->first(); 
        $orr =   DB::table('orders')
                ->where('cart_id',$cart_id)
                ->first();
                    
           $v = DB::table('store_orders')
 		   ->where('order_cart_id',$cart_id)
 		   ->get(); 
          foreach($v as $vs){
                $qt = $vs->qty;
                $pr = DB::table('store_products')
            ->join('product_varient','store_products.varient_id','=','product_varient.varient_id') 
            ->join('product','product_varient.product_id','=','product.product_id')
           ->where('store_products.varient_id',$vs->varient_id)
           ->where('store_products.store_id',$store_id)
           ->first();
                 $stoc = DB::table('store_products')
                    ->where('varient_id',$vs->varient_id)
                    ->where('store_id',$store_id) 
                    ->first();
              if($stoc){     
                $newstock = $stoc->stock - $qt;     
                $st = DB::table('store_products')
                    ->where('varient_id',$vs->varient_id)
                    ->where('store_id',$store_id)
                    ->update(['stock'=>$newstock]);
              }
             }       
            $orderconfirm = DB::table('orders')
                    ->where('cart_id',$cart_id)
                    ->update(['order_status'=>'Confirmed',
                    'dboy_id'=>$dboyid]);
         
 		   
         if($orderconfirm){
                $notification_title = "You Got a New Order for Delivery on ".$orr->delivery_date;
                $notification_text = "you got an order with cart id #".$cart_id." of price ".$curr->currency_sign." " .$orr->total_price. ". It will have to delivered on ".$orr->delivery_date." between ".$orr->time_slot.".";
                
                $date = date('d-m-Y');
                $getUser = DB::table('delivery_boy')
                                ->get();
        
                $created_at = Carbon::now();
        
                
                $getFcm = DB::table('fcm')
                            ->where('id', '1')
                            ->first();
                            
                $getFcmKey = $getFcm->driver_server_key;
                $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
                $token = $getDevice->device_id;
                    
        
                    $notification = [
                        'title' => $notification_title,
                        'body' => $notification_text,
                        'sound' => true,
                    ];
                    
                    $extraNotificationData = [
                        "dboy_id"=>$dboyid,
                        "cart_id" => $cart_id,
                        'title' => "New Delivery Order",
                        'body' => $notification_text,
                        ];
        
                    $fcmNotification = [
                        'to'        => $token,
                        'data' => $extraNotificationData,
                        'content_available' => false, //important for iOS
                        'priority' => "high",
                        // 'time_to_live' => 5000,
                        'requireInteraction'=> true,
                        'actions'=> [
                            'action'=> "accept",
                            'title'=> "Accept"
                        ],[
                            'action'=> "reject",
                            'title'=> "Reject"
                        ]
                    ];
        
                    $headers = [
                        'Authorization: key='.$getFcmKey,
                        'Content-Type: application/json'
                    ];
        
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL,$fcmUrl);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
                    $result = curl_exec($ch);
                    curl_close($ch);
                   $results = json_decode($result);
             
             
        	$message = array('status'=>'1', 'message'=>'order is confirmed and Assigned to '.$getDevice->boy_name);
	        return $message;
              }
    	else{
    		$message = array('status'=>'0', 'message'=>'Already Assigned to '.$getDevice->boy_name);
	        return $message;
    	} 
 }
 public function delivery_boy_list (Request $request)
     {
         $store_id = $request->store_id;
         $store= DB::table('store')
    	 		   ->where('store_id',$store_id)
    	 		   ->first();
    	 		   
    	  $nearbydboy = DB::table('delivery_boy')
                ->leftJoin('orders', 'delivery_boy.dboy_id', '=', 'orders.dboy_id') 
                ->select("delivery_boy.boy_name","delivery_boy.dboy_id","delivery_boy.lat","delivery_boy.lng","delivery_boy.boy_city",DB::raw("Count(orders.order_id)as count"),DB::raw("6371 * acos(cos(radians(".$store->lat . ")) 
                * cos(radians(delivery_boy.lat)) 
                * cos(radians(delivery_boy.lng) - radians(" . $store->lng . ")) 
                + sin(radians(" .$store->lat. ")) 
                * sin(radians(delivery_boy.lat))) AS distance"))
               ->groupBy("delivery_boy.boy_name","delivery_boy.dboy_id","delivery_boy.lat","delivery_boy.lng","delivery_boy.boy_city")
               ->where('delivery_boy.boy_city', $store->city)
               ->where('delivery_boy.status','1')
               ->orderBy('distance')
               ->get();  	
               
        if (count($nearbydboy)>0){
            $message = array('status'=>'1', 'message'=>'Delivery Boy List', 'data'=>$nearbydboy);
	        return $message;
              }
    	else{
    		$message = array('status'=>'0', 'message'=>'No Delivery Boy In Your City');
	        return $message;
    	} 
    	
   }
   
   
    public function storeconfirm(Request $request)
    {
       $cart_id= $request->cart_id;
       $dboyid = $request->dboy_id;
       $store_id = $request->store_id;
      
       $curr = DB::table('currency')
             ->first();
       
        $store= DB::table('store')
        	->where('store_id',$store_id)
    	 	->first();
       
        $getDevice = DB::table('delivery_boy')
             ->where('dboy_id', $dboyid)
            ->select('device_id','boy_name')
            ->first(); 
        $orr =   DB::table('orders')
                ->where('cart_id',$cart_id)
                ->first();
                    
           $v = DB::table('store_orders')
 		   ->where('order_cart_id',$cart_id)
 		   ->get(); 
          foreach($v as $vs){
                $qt = $vs->qty;
                $pr = DB::table('store_products')
            ->join('product_varient','store_products.varient_id','=','product_varient.varient_id') 
            ->join('product','product_varient.product_id','=','product.product_id')
           ->where('store_products.varient_id',$vs->varient_id)
           ->where('store_products.store_id',$store_id)
           ->first();
                 $stoc = DB::table('store_products')
                    ->where('varient_id',$vs->varient_id)
                    ->where('store_id',$store_id) 
                    ->first();
              if($stoc){     
                $newstock = $stoc->stock - $qt;     
                $st = DB::table('store_products')
                    ->where('varient_id',$vs->varient_id)
                    ->where('store_id',$store_id)
                    ->update(['stock'=>$newstock]);
              }
             }       
            $orderconfirm = DB::table('orders')
                    ->where('cart_id',$cart_id)
                    ->update(['order_status'=>'Confirmed',
                    'dboy_id'=>$dboyid]);
         
 		   
         if($orderconfirm){
                $notification_title = "You Got a New Order for Delivery on ".$orr->delivery_date;
                $notification_text = "you got an order with cart id #".$cart_id." of price ".$curr->currency_sign." " .$orr->total_price. ". It will have to delivered on ".$orr->delivery_date." between ".$orr->time_slot.".";
                
                $date = date('d-m-Y');
                $getUser = DB::table('delivery_boy')
                                ->get();
        
                $created_at = Carbon::now();
        
                
                $getFcm = DB::table('fcm')
                            ->where('id', '1')
                            ->first();
                            
                $getFcmKey = $getFcm->driver_server_key;
                $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
                $token = $getDevice->device_id;
                    
        
                    $notification = [
                        'title' => $notification_title,
                        'body' => $notification_text,
                        'sound' => true,
                    ];
                    
                    $extraNotificationData = ["message" => $notification];
        
                    $fcmNotification = [
                        'to'        => $token,
                        'notification' => $notification,
                        'data' => $extraNotificationData,
                    ];
        
                    $headers = [
                        'Authorization: key='.$getFcmKey,
                        'Content-Type: application/json'
                    ];
        
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL,$fcmUrl);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
                    $result = curl_exec($ch);
                    curl_close($ch);
                   $results = json_decode($result);
             
             
        	$message = array('status'=>'1', 'message'=>'order is confirmed and Assigned to '.$getDevice->boy_name);
	        return $message;
              }
    	else{
    		$message = array('status'=>'0', 'message'=>'Already Assigned to '.$getDevice->boy_name);
	        return $message;
    	} 
   
    }
   
   
   
}      