<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class AppController extends Controller
{
 
    public function app(Request $request)
    {
          $app = DB::table('tbl_web_setting')
                      ->first();
                      
        if($app)   {
            $message = array('status'=>'1', 'message'=>'App Name & Logo', 'data'=>$app,'time'=>date('Y-m-d H:i:s'));
            return $message;
        }
        else{
            $message = array('status'=>'0', 'message'=>'data not found', 'data'=>[]);
            return $message;
        }

        return $message;
    }
    
    public function delivery_info(Request $request)
    {
        /*$del_fee = DB::table('freedeliverycart')
                      ->first();
                      
        if($del_fee)   { 
            $message = array('status'=>'1', 'message'=>'Delivery fee and cart value', 'data'=>$del_fee);
            return $message;
        }
        else{
            $message = array('status'=>'0', 'message'=>'data not found', 'data'=>[]);
            return $message;
        }
        return $message;*/
        $data= $request->order_array;
        $data_array = json_decode($data);
        $user_id= $request->user_id;
        $delivery_date = $request-> delivery_date;
        $time_slot= $request->time_slot;
        $store_id = $request->store_id;
        
        $user = DB::table('address')
            ->select('society','city','lat','lng','address_id')
            ->where('user_id', $user_id)
            ->where('select_status', 1)
            ->first();
       if(!$user ){
           	$message = array('status'=>'0', 'message'=>'Select any Address - '.json_encode($request));
        	return $message;
       }
       $cart_price = 0.0;
       $weight = 0.0;
       $cat_id = 0;
       foreach ($data_array as $h){
            $varient_id = $h->varient_id;
            $p =  DB::table('store_products')
                    ->join ('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                    ->join('product','product_varient.product_id','=','product.product_id')
                ->Leftjoin('deal_product','product_varient.varient_id','=','deal_product.varient_id')
                ->where('product_varient.varient_id',$varient_id)
                ->where('store_products.store_id',$store_id)
                ->first();
            if($p->deal_price != NULL &&  $p->valid_from < $current && $p->valid_to > $current){
                $price= $p->deal_price;    
            }else{
                $price = $p->price;
            }
            
            $cat_id = $p->cat_id;
            
            $mrpprice = $p->mrp;
            $order_qty = $h->qty;
            
            $cart_price += $price*$order_qty;
            $weight += $p->weight*$order_qty;
        }
        $store = DB::table('store')
                ->select('lat','lng')
                ->where('store_id',$store_id)
                ->first();
                
        $delivery = DB::table('delivery_charge')
                    ->where('cat_id',$cat_id)
                    ->first();
        if(!empty($delivery)){
            $min = explode('*',$delivery->min);
            $max = explode('*',$delivery->max);
            $charge = explode('*',$delivery->charge);
            $delivery_charge = 0.0;
            
            if($delivery->charge_type=='by_weight'){
                $value = $weight;
            }else if($delivery->charge_type=='by_distance'){
                $value = $this->get_distance($user->lat,$user->lng,$store->lat,$store->lng);
            }else {
                $value = $cart_price;
            }
            for($i=0;$i<sizeof($min);$i++){
                if($value >= $min[$i] && $value <= $max[$i]){
                    $delivery_charge = $charge[$i];
                    break;
                }
            }
            
            $message = array('status'=>'1', 'message'=>'Delivery fee and cart value','charge_type'=>$delivery->charge_type,'value'=>$value, 'del_charge'=>$delivery_charge);
            return $message;
        }else{
            $del_fee = DB::table('freedeliverycart')
                        ->first();
                          
            if($del_fee)   { 
                $message = array('status'=>'1', 'message'=>'Delivery fee and cart value', 'del_charge'=>$del_fee->del_charge);
                return $message;
            }
            else{
                $message = array('status'=>'0', 'message'=>'data not found');
                return $message;
            }
        }
        
    }
    
    function get_distance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo)
    {
        $rad = M_PI / 180;
        //Calculate distance from latitude and longitude
        $theta = $longitudeFrom - $longitudeTo;
        $dist = sin($latitudeFrom * $rad) 
            * sin($latitudeTo * $rad) +  cos($latitudeFrom * $rad)
            * cos($latitudeTo * $rad) * cos($theta * $rad);
    
        return acos($dist) / $rad * 60 *  1.853;
    }
}
