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
            $orderRating->review  = $request->order_review;
            $orderRating->rating = $request->order_rating;
            $orderRating->save();

            $delRating = new DeliveryRating();
            $delRating->order_id = $order->order_id;
            $delRating->review  = $request->delivery_review;
            $delRating->rating = $request->delivery_rating;
            $delRating->save();

            $ratings = json_decode($request->ratings);
            foreach($ratings as $rating){
                if($rating->rating!=null && $rating->rating > 0){
                    $productRating = new OrderRating();
                    $productRating->order_id = $order->order_id;
                    $productRating->product_id = $rating->product_id;
                    $productRating->review = $rating->review;
                    $productRating->rating = $rating->rating;
                    $productRating->save();
                }
            }
            return array("success"=>true,"message"=>'Thank You for Rating.');
        }
    }
}