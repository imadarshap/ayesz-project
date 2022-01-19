<?php

namespace App\Http\Controllers\Api;

use App\DeliveryRating;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Order;
use App\OrderRating;

class OrderRatingsController extends Controller
{
    public function rateOrder(Request $request){
        if(!empty($request->cart_id)){
            $order = Order::where('cart_id',$request->cart_id)->where('user_id',$request->user_id)->first();
            if(empty($order)){
                return array("success"=>false,"message"=>'Invalid Order');
            }
            $orderRating = new OrderRating();
            $orderRating->order_id = $order->order_id;
            $orderRating->review  = !empty($request->order_review)?$request->order_review:null;
            $orderRating->rating = !empty($request->order_rating)?$request->order_rating:null;
            $orderRating->save();

            $delRating = new DeliveryRating();
            $delRating->order_id = $order->order_id;
            $delRating->review  = !empty($request->delivery_review)?$request->delivery_review:null;
            $delRating->rating = !empty($request->delivery_rating)?$request->delivery_rating:null;
            $delRating->save();

            $ratings = json_decode($request->ratings);
            foreach($ratings as $rating){
                if($rating->rating!=null && $rating->rating > 0){
                    $productRating = new OrderRating();
                    $productRating->order_id = $order->order_id;
                    $productRating->product_id = $rating->product_id;
                    $productRating->review = (!empty($rating->review))?$rating->review:null;
                    $productRating->rating = (!empty($rating->rating))?$rating->rating:null;
                    $productRating->save();
                }
            }
            return array("success"=>true,"message"=>'Thank You for Rating.');
        }else{
            return array("success"=>false,"message"=>'Insufficient Parameteres');
        }
    }

    public function getProductReviews(Request $request){
        if(!empty($request->product_id) && !empty($request->store_id)){
            $reviews = OrderRating::where('product_id',$request->product_id)
                        ->join('orders','orders.order_id','order_ratings.order_id')
                        ->where('orders.store_id',$request->store_id)
                        ->join('users','users.user_id','orders.user_id')
                        ->select('order_ratings.*','users.user_name')
                        ->get();
            return array("success"=>true,'data'=>$reviews);
        }else{
            return array("success"=>false,"message"=>'Insufficient Parameteres','params'=>$request->store_id);
        }
    }
}