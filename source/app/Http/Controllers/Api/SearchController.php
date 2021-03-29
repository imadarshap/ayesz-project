<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use DateTime;

class SearchController extends Controller
{
    public function search(Request $request)
    {
       $keyword = $request->keyword;
       $lat = $request->lat;
       $lng = $request->lng;
    	$city = $request->city;
       
       $nearbystore = DB::table('store')
                    ->select('del_range','store_id',DB::raw("6371 * acos(cos(radians(".$lat . "))
                    * cos(radians(store.lat)) 
                    * cos(radians(store.lng) - radians(" . $lng . ")) 
                    + sin(radians(" .$lat. ")) 
                    * sin(radians(store.lat))) AS distance"))
                  ->where('store.del_range','>=','distance')
                //   ->where('store.city',$city)
                  ->orderBy('distance')
                  ->first();
    
    $products = DB::table('store')->select('store.store_id','store_name','product.product_name','product.product_id','product_varient.varient_id','product_varient.quantity',
                                         'product_varient.unit','product_varient.varient_image','product_varient.description','product.cat_id','store.availability',
                                         'store_products.mrp','store_products.price','store_products.stock',
                     DB::raw("(6371 * acos(cos(radians(" . $lat . "))
                    * cos(radians(store.lat))
                    * cos(radians(store.lng) - radians(" . $lng . "))
                    + sin(radians(" . $lat . "))
                    * sin(radians(store.lat)))) AS distance"))
        	->join('store_products','store_products.store_id','store.store_id')
    		->join ('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
			->join ('product', 'product_varient.product_id', '=', 'product.product_id')
    		->where('product.product_name', 'like', '%'.$keyword.'%')
        // 	->where('store.city','=',$city)
        	->whereRaw("del_range >= (6371 * acos(cos(radians(" . $lat . "))
                    * cos(radians(store.lat))
                    * cos(radians(store.lng) - radians(" . $lng . "))
                    + sin(radians(" . $lat . "))
                    * sin(radians(store.lat))))")
            ->orderBy('distance')
            ->get();
    $products_arr = array();
    foreach($products as $product){
        $open = true;
        $avails = DB::table('vendor_availability')->where('store_id',$product->store_id)->where('day',date('D'))->first();
        if(!empty($avails) && $avails->status==1){
            $start_time = explode(',',$avails->start_time);
            $end_time = explode(',',$avails->end_time);
            if($start_time[0]=='24'){
                $open = true;
            }else{
                for($i=0; $i < sizeof($start_time) ; $i++){
                    $curr_time = new DateTime("now");
                    $st_time = new DateTime(date('Y-m-d').' '.$start_time[$i].':00');
                    $en_time = new DateTime(date('Y-m-d').' '.$end_time[$i].':00');
                    
                    if($curr_time >$st_time && $curr_time < $en_time)
                    {
                        $open = true;
                    }else{
                        $open = false;
                    }
                }
            }
        }else if(!empty($avails)){
            $open = false;
        }
        if($product->availability==0){
            $open = false;
        }
        $product = (array)$product;
        if($open){
            $product['available'] = 1;
            
        }else{
            $product['available'] = 0;
        }
        array_push($products_arr,$product);
    }
    if(count($products)==0){
        $message = array('status'=>'1', 'message'=>'Sorry, No Product Found', 'data'=>$products_arr);
    }else{
        $message = array('status'=>'1', 'message'=>'Sorry, No Products available at this moment', 'data'=>$products_arr);
    }
    return $message;
    
    /*if($nearbystore->del_range >= $nearbystore->distance)  {
        $prod = DB::table('store_products')
                 ->join ('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
			     ->join ('product', 'product_varient.product_id', '=', 'product.product_id')
			     ->select('product.product_name','product.product_id')
                 ->groupBy('product.product_name','product.product_id')
                 ->where('store_products.store_id', $nearbystore->store_id)
                ->where('product.product_name', 'like', '%'.$keyword.'%')
                ->get();

        if(count($prod)>0){
            $result =array();
            $i = 0;

            foreach ($prod as $prods) {
                array_push($result, $prods);

                $app = json_decode($prods->product_id);
                $apps = array($app);
                $store_id = DB::table('store_products')
					   ->join ('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                     ->Leftjoin('deal_product','product_varient.varient_id','=','deal_product.varient_id')
                	->Leftjoin('product','product.product_id','product_varient.product_id')
                         ->select('store_products.store_id','store_products.stock','product_varient.varient_id', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image','product_varient.unit','product_varient.quantity','deal_product.deal_price', 'deal_product.valid_from', 
                                  'deal_product.valid_to','product.cat_id')
                        ->whereIn('product_varient.product_id', $apps)
                        ->get();
                        
                $result[$i]->varients = $app;
                $i++;
            }

            $message = array('status'=>'1', 'message'=>'Products found', 'data'=>$prod);
            return $message;
        }
        else{
            $message = array('status'=>'0', 'message'=>'Products not found', 'data'=>[]);
            return $message;
        }
      }
       else{
           $message = array('status'=>'2', 'message'=>'No Products Found Nearby', 'data'=>[]);
            return $message; 
       }*/
    }
}
