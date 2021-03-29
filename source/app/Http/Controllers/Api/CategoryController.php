<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;
use DateTime;
use App\Helper\Helper;

class CategoryController extends Controller
{
    public function cate(Request $request)
    {
        $cat = DB::table('categories')->where('level', 0)
            ->get();

        if (count($cat) > 0)
        {
            $result = array();
            $i = 0;

            foreach ($cat as $cats)
            {
                array_push($result, $cats);

                $app = json_decode($cats->cat_id);
                $apps = array(
                    $app
                );
                $app = DB::table('categories')->whereIn('parent', $apps)->where('level', 1)
                    ->get();

                $result[$i]->subcategory = $app;
                $i++;
                $res = array();
                $j = 0;
                foreach ($app as $appss)
                {
                    array_push($res, $appss);
                    $c = array(
                        $appss->cat_id
                    );
                    $app1 = DB::table('categories')->whereIn('parent', $c)->where('level', 2)
                        ->get();
                    if (count($app1) > 0)
                    {
                        $res[$j]->subchild = $app1;
                        $j++;
                    }
                    else
                    {
                        $res[$j]->subchild = [];
                        $j++;
                    }
                }

            }

            $message = array(
                'status' => '1',
                'message' => 'data found',
                'data' => $cat
            );
            return $message;
        }
        else
        {
            $message = array(
                'status' => '0',
                'message' => 'data not found',
                'data' => []
            );
            return $message;
        }
    }

    public function cat_product(Request $request)
    {
        $cat_id = $request->cat_id;
        $lat = $request->lat;
        $lng = $request->lng;
        $cityname = $request->city;
        $city = ucfirst($cityname);
        $nearbystore = DB::table('store')->select('del_range', 'store_id', DB::raw("6371 * acos(cos(radians(" . $lat . ")) 
                    * cos(radians(store.lat)) 
                    * cos(radians(store.lng) - radians(" . $lng . ")) 
                    + sin(radians(" . $lat . ")) 
                    * sin(radians(store.lat))) AS distance"))->where('store.del_range', '>=', 'distance')
            ->orderBy('distance')
            ->first();
        if ($nearbystore->del_range >= $nearbystore->distance)
        {
            $products = DB::table('store_products')->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                ->where('product.cat_id', $cat_id)->where('store_products.store_id', $nearbystore->store_id)
                ->where('store_products.price', '!=', NULL)
                ->where('product.hide', 0)
                ->get();

            if (count($products) > 0)
            {
                $result = array();
                $i = 0;

                /*foreach ($prod as $prods)
                {
                    array_push($result, $prods);

                    $app = json_decode($prods->product_id);
                    $apps = array(
                        $app
                    );
                    $app = DB::table('store_products')->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                        ->Leftjoin('deal_product', 'product_varient.varient_id', '=', 'deal_product.varient_id')
                    	->Leftjoin('product','product.product_id','product_varient.product_id')
                        ->select('store_products.store_id', 'store_products.stock', 'product_varient.varient_id', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'deal_product.deal_price', 'deal_product.valid_from', 'deal_product.valid_to','product.cat_id')
                        ->where('store_products.store_id', $nearbystore->store_id)
                        ->whereIn('product_varient.product_id', $apps)->where('store_products.price', '!=', NULL)
                        ->get();

                    $result[$i]->varients = $app;
                    $i++;

                }*/
                
                $products_arr = array();
                foreach($products as $product){
                    $open = false;
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
                        if($product->availability==0){
                            $open = false;
                        }
                    }
                    $product = (array)$product;
                    if($open){
                        $product['available'] = 1;
                    }else{
                        $product['available'] = 0;
                    }
                    array_push($products_arr,$product);
                }

                $message = array(
                    'status' => '1',
                    'message' => 'Products found',
                    'data' => $products_arr
                );
                return $message;
            }
            else
            {
                $message = array(
                    'status' => '0',
                    'message' => 'Products not found',
                    'data' => []
                );
                return $message;
            }
        }
        else
        {
            $message = array(
                'status' => '2',
                'message' => 'No Products Found Nearby',
                'data' => []
            );
            return $message;
        }

    }

    public function cat(Request $request)
    {
        $cat = DB::table('categories')->where('level', 0)
            ->get();

        if (count($cat) > 0)
        {
            $result = array();
            $i = 0;

            foreach ($cat as $cats)
            {
                array_push($result, $cats);

                $app = json_decode($cats->cat_id);
                $apps = array(
                    $app
                );
                $app = DB::table('categories')->whereIn('parent', $apps)->where('level', 1)
                    ->get();

                if (count($app) > 0)
                {
                    $result[$i]->subcategory = $app;
                    $i++;
                    $res = array();
                    $j = 0;
                    foreach ($app as $appss)
                    {
                        array_push($res, $appss);
                        $c = array(
                            $appss->cat_id
                        );
                        $app1 = DB::table('categories')->whereIn('parent', $c)->where('level', 2)
                            ->get();
                        if (count($app1) > 0)
                        {
                            $res[$j]->subchild = $app1;
                            $j++;
                            $res2 = array();
                            $k = 0;
                            foreach ($app1 as $apps1)
                            {
                                array_push($res2, $apps1);
                                $catt = array(
                                    $apps1->cat_id
                                );
                                $prod = DB::table('product')->whereIn('cat_id', $catt)->get();

                                $res2[$k]->product = $prod;
                                $k++;
                            }

                        }
                        else
                        {
                            $pr = DB::table('product')->whereIn('cat_id', $c)->get();
                            $res[$j]->product = $pr;
                            $j++;
                        }
                    }
                }
                else
                {
                    $app = DB::table('product')->whereIn('cat_id', $apps)->get();
                    $result[$i]->product = $app;
                    $i++;
                }
            }

            $message = array(
                'status' => '1',
                'message' => 'data found',
                'data' => $cat
            );
            return $message;
        }
        else
        {
            $message = array(
                'status' => '0',
                'message' => 'data not found',
                'data' => []
            );
            return $message;
        }
    }

    public function varient(Request $request)
    {
        $prod_id = $request->product_id;
        $lat = $request->lat;
        $lng = $request->lng;
        $cityname = $request->city;
        $store_id = $request->store_id;
        
        $city = ucfirst($cityname);
        $varient = DB::table('store_products')->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                ->Leftjoin('deal_product', 'product_varient.varient_id', '=', 'deal_product.varient_id')
            	->join('product','product.product_id','product_varient.product_id')
                ->select('store_products.store_id', 'store_products.stock', 'product_varient.varient_id', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'deal_product.deal_price', 'deal_product.valid_from', 'deal_product.valid_to','product.cat_id')
                ->where('product_varient.product_id', $prod_id)->where('store_products.price', '!=', NULL)
                ->where('store_products.store_id', $store_id)
                ->get();
            if (count($varient) > 0)
            {
                $message = array(
                    'status' => '1',
                    'message' => 'varients',
                    'data' => $varient
                );
                return $message;
            }
            else
            {
                $message = array(
                    'status' => '0',
                    'message' => 'data not found',
                    'data' => []
                );
                return $message;
            }

    }

    public function dealproduct(Request $request)
    {
        $d = Carbon::Now();
        $lat = $request->lat;
        $lng = $request->lng;
        $cityname = $request->city;
        $city = ucfirst($cityname);

        $nearbystore = DB::table('store')->select('del_range', 'store_id', DB::raw("6371 * acos(cos(radians(" . $lat . ")) 
                    * cos(radians(store.lat)) 
                    * cos(radians(store.lng) - radians(" . $lng . ")) 
                    + sin(radians(" . $lat . ")) 
                    * sin(radians(store.lat))) AS distance"))->where('store.del_range', '>=', 'distance')
            ->orderBy('distance')
            ->first();
        if ($nearbystore->del_range >= $nearbystore->distance)
        {
            $deal_p = DB::table('deal_product')->join('store_products', 'deal_product.varient_id', '=', 'store_products.varient_id')
                ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                ->select('store_products.store_id', 'store_products.stock', 'deal_product.deal_price as price', 'product_varient.varient_image', 'product_varient.quantity', 'product_varient.unit', 'store_products.mrp', 'product_varient.description', 'product.product_name', 'product.product_image', 'product_varient.varient_id', 'product.product_id', 'deal_product.valid_to', 'deal_product.valid_from')
                ->groupBy('store_products.store_id', 'store_products.stock', 'deal_product.deal_price', 'product_varient.varient_image', 'product_varient.quantity', 'product_varient.unit', 'store_products.mrp', 'product_varient.description', 'product.product_name', 'product.product_image', 'product_varient.varient_id', 'product.product_id', 'deal_product.valid_to', 'deal_product.valid_from')
                ->where('store_products.store_id', $nearbystore->store_id)
                ->whereDate('deal_product.valid_from', '<=', $d->toDateString())
                ->WhereDate('deal_product.valid_to', '>', $d->toDateString())
                ->where('store_products.price', '!=', NULL)
                ->where('product.hide', 0)
                ->get();

            if (count($deal_p) > 0)
            {
                $result = array();
                $i = 0;
                $j = 0;
                foreach ($deal_p as $deal_ps)
                {
                    array_push($result, $deal_ps);

                    $val_to = $deal_ps->valid_to;
                    $diff_in_minutes = $d->diffInMinutes($val_to);
                    $totalDuration = $d->diff($val_to)->format('%H:%I:%S');
                    $result[$i]->timediff = $diff_in_minutes;
                    $i++;
                    $result[$j]->hoursmin = $totalDuration;
                    $j++;
                }

                $message = array(
                    'status' => '1',
                    'message' => 'Products found',
                    'data' => $deal_p
                );
                return $message;
            }
            else
            {
                $message = array(
                    'status' => '0',
                    'message' => 'Products not found',
                    'data' => []
                );
                return $message;
            }
        }
        else
        {
            $message = array(
                'status' => '2',
                'message' => 'No Products Found Nearby',
                'data' => []
            );
            return $message;
        }

    }

    public function top_six(Request $request)
    {
        $lat = $request->lat;
        $lng = $request->lng;
        $cityname = $request->city;
        $city = ucfirst($cityname);
        $nearbystore = DB::table('store')->select('del_range', 'store_id', DB::raw("6371 * acos(cos(radians(" . $lat . ")) 
                    * cos(radians(store.lat)) 
                    * cos(radians(store.lng) - radians(" . $lng . ")) 
                    + sin(radians(" . $lat . ")) 
                    * sin(radians(store.lat))) AS distance"))->where('store.del_range', '>=', 'distance')
            ->orderBy('distance')
            ->first();
        if ($nearbystore->del_range >= $nearbystore->distance)
        {
            $topsix = DB::table('store_products')->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                ->Leftjoin('store_orders', 'product_varient.varient_id', '=', 'store_orders.varient_id')
                ->Leftjoin('orders', 'store_orders.order_cart_id', '=', 'orders.cart_id')
                ->join('categories', 'product.cat_id', '=', 'categories.cat_id')
                ->select('categories.title', 'categories.image', 'categories.description', 'categories.cat_id', DB::raw('count(store_orders.varient_id) as count'))
                ->groupBy('categories.title', 'categories.image', 'categories.description', 'categories.cat_id')
                ->where('store_products.store_id', $nearbystore->store_id)
                ->where('store_products.price', '!=', NULL)
                ->where('product.hide', 0)
                ->orderBy('count', 'desc')
                ->limit(6)
                ->get();
            if (count($topsix) > 0)
            {
                $message = array(
                    'status' => '1',
                    'message' => 'Top Six Categories',
                    'data' => $topsix
                );
                return $message;
            }
            else
            {
                $message = array(
                    'status' => '0',
                    'message' => 'Nothing in Top Six',
                    'data' => []
                );
                return $message;
            }
        }
        else
        {
            $message = array(
                'status' => '2',
                'message' => 'No Products Found Nearby',
                'data' => []
            );
            return $message;
        }
    }
    public function homecat(Request $request)
    {
        $lat = $request->lat;
        $lng = $request->lng;

        $nearbystore = DB::table('store')->select('del_range', 'store_id', DB::raw("6371 * acos(cos(radians(" . $lat . ")) 
                    * cos(radians(store.lat)) 
                    * cos(radians(store.lng) - radians(" . $lng . ")) 
                    + sin(radians(" . $lat . ")) 
                    * sin(radians(store.lat))) AS distance"))->where('store.del_range', '>=', 'distance')
            ->orderBy('distance')
            ->first();
        if ($nearbystore->del_range >= $nearbystore->distance)
        {
            $category = DB::table('tbl_top_cat')->join('categories', 'tbl_top_cat.cat_id', '=', 'categories.cat_id')
                ->join('product', 'categories.cat_id', '=', 'product.cat_id')
                ->join('product_varient', 'product.product_id', '=', 'product_varient.product_id')
                ->join('store_products', 'product_varient.varient_id', '=', 'store_products.varient_id')
                ->select('categories.cat_id', 'categories.title', 'categories.image')
                ->groupBy('categories.cat_id', 'categories.title', 'categories.image')
                ->where('store_products.store_id', $nearbystore->store_id)
                ->where('store_products.price', '!=', NULL)
                ->where('product.hide', 0)
                ->orderBy('tbl_top_cat.cat_rank', 'ASC')
                ->get();

            if (count($category) > 0)
            {
                $message = array(
                    'status' => '1',
                    'message' => 'Home Categories',
                    'data' => $category
                );
                return $message;
            }
            else
            {
                $message = array(
                    'status' => '0',
                    'message' => 'Nothing in Home Category',
                    'data' => []
                );
                return $message;
            }
        }
        else
        {
            $message = array(
                'status' => '2',
                'message' => 'No Products Found Nearby',
                'data' => []
            );
            return $message;
        }
    }

    public function stores_by_category(Request $request)
    {
        $lat = $request->lat;
        $lng = $request->lng;
        $cityname = $request->city;
        $city = ucfirst($cityname);
    	$cat_id = $request->cat_id;

        $stores = DB::table('store')->select('store.store_id','store_name','employee_name','phone_number','city','address','lat','lng','store.del_range','categories.image','store.availability',
                     DB::raw("(6371 * acos(cos(radians(" . $lat . "))
                    * cos(radians(store.lat))
                    * cos(radians(store.lng) - radians(" . $lng . "))
                    + sin(radians(" . $lat . "))
                    * sin(radians(store.lat)))) AS distance"))
        	->join('store_categories',function($join)use ($cat_id){
                    $join->on('store_categories.store_id','=','store.store_id')
                    ->where('cat_id',$cat_id);
                    })
        	->join('categories','categories.cat_id','store_categories.cat_id')
        // 	->where('store.city','=',$city)
        	->whereRaw("del_range >= (6371 * acos(cos(radians(" . $lat . "))
                    * cos(radians(store.lat))
                    * cos(radians(store.lng) - radians(" . $lng . "))
                    + sin(radians(" . $lat . "))
                    * sin(radians(store.lat))))")
            ->orderByRaw('store_categories.store_priority=0,store_categories.store_priority')
        
            ->get();
    
        $stores_arr = array();
        if(count($stores)>0){
            foreach($stores as $store){
                $today = DB::table('vendor_availability')->where('store_id',$store->store_id)->where('day',strtolower(date('D')))->first();
                
                $openTime = "Closed";
                $avails = DB::table('vendor_availability')->where('store_id',$store->store_id)->get();
                $start = false;
                $will_open = false;
                for($i=0;$i < count($avails);$i++){
                    if($start){
                        if($avails[$i]->status==1){
                            $start_time = explode(',',$avails[$i]->start_time);
                            if($start_time[0]=='24'){
                                $openTime = "Opens at 12:00 AM";
                            }else if(count($start_time)>0){ 
                                $st_time = new DateTime(date('Y-m-d').' '.$start_time[0].':00');
                                if(strtolower(date('D',strtotime(' +1 day')))==$avails[$i]->day)
                                    $openTime = "Opens tomorrow at ".$st_time->format('h:i a');
                                else
                                    $openTime = "Opens on ".Helper::getFullDay($avails[$i]->day)." at ".$st_time->format('h:i a');
                            }
                            $will_open = true;
                            break;
                        }
                    }
                    if($avails[$i]->day==strtolower(date('D'))){
                        $start = !$start;  
                        if($start==false){
                            break;
                        }
                    }
                    if($i==count($avails)-1){
                        $i=-1;
                    } 
                }
                if(!$will_open){
                    $openTime = 'Temporarily closed';
                }
                
                $tomorrow = DB::table('vendor_availability')->where('store_id',$store->store_id)->where('day',strtolower(date('D',strtotime(' +1 day'))))->first();
                $store = (array)$store;
                $store['today_schedule'] = $today;
                $store['tomorrow_schedule'] = $tomorrow;
                $store['open_time'] = $openTime;
                array_push($stores_arr,$store);
            }
        }
    
    	$message = array(
                    'status' => '1',
                    'message' => 'Near By Vendors',
                    'data' => $stores_arr
                );
        return $message;
    }

	public function products_by_store(Request $request)
    {
        $store_id = $request->store_id;
    	$cat_id = $request->cat_id;

        	$products = DB::table('store_products')->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                ->join('store','store.store_id','store_products.store_id')
                ->where('product.cat_id', $cat_id)->where('store_products.store_id', $store_id)
                ->where('store_products.price', '!=', NULL)
                ->where('product.hide', 0)
            	->orderByRaw('store_products.stock=0,product.product_name')
                ->get();

            if (count($products) > 0)
            {
                /*$result = array();
                $i = 0;

                foreach ($prod as $prods)
                {
                    array_push($result, $prods);

                    $app = json_decode($prods->product_id);
                    $apps = array(
                        $app
                    );
                    $app = DB::table('store_products')->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                        ->Leftjoin('deal_product', 'product_varient.varient_id', '=', 'deal_product.varient_id')
                        ->select('store_products.store_id', 'store_products.stock', 'product_varient.varient_id', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'deal_product.deal_price', 'deal_product.valid_from', 'deal_product.valid_to')
                        ->where('store_products.store_id', $store_id)
                        ->whereIn('product_varient.product_id', $apps)->where('store_products.price', '!=', NULL)
                        ->get();

                    $result[$i]->varients = $app;
                    $i++;

                }*/
                
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

                $message = array(
                    'status' => '1',
                    'message' => 'Products found',
                    'data' => $products_arr
                );
                return $message;
            }
            else
            {
                $message = array(
                    'status' => '0',
                    'message' => 'Products not found',
                    'data' => []
                );
                return $message;
            }
    }

}

