<?php

namespace App\Http\Controllers\Storeapi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;
use App\Traits\SendSms;
use App\WalletHistory;

class StoreorderController extends Controller
{
    use SendSms;
    public function storeorders(Request $request)
    {
        switch ($request->date) {
            case "today":
                $date = date('Y-m-d');
                $to_date = $date;
                break;
            case "yesterday":
                $date = date('Y-m-d', strtotime("-1 days"));
                $to_date = $date;
                break;
            case "date_range":
                $date = $request->from_date;
                $to_date = $request->to_date;
                break;
        }
        $store_id = $request->store_id;
        $store = DB::table('store')
            ->where('store_id', $store_id)
            ->first();

        $ord = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.user_id')
            ->leftJoin('address', 'orders.address_id', '=', 'address.address_id')
            ->leftJoin('delivery_boy', 'orders.dboy_id', '=', 'delivery_boy.dboy_id')
            ->select('orders.cart_id', 'users.user_name', 'users.user_phone', 'orders.delivery_date', 'orders.delivery_charge', 'orders.total_price', 'orders.delivery_charge', 'orders.rem_price', 'orders.payment_status', 'delivery_boy.boy_name', 'delivery_boy.boy_phone', 'orders.time_slot', 'orders.order_status', 'orders.payment_method', 'users.user_phone', 'address.*')
            ->where('orders.store_id', $store_id)
            ->where('orders.delivery_date', '>=', $date)
            ->where('orders.delivery_date', '<=', $to_date)
            ->where('payment_method', '!=', NULL)
            // ->where('orders.order_status','!=','cancelled')
            // ->where('orders.order_status','!=','Completed')
            ->orderByRaw("FIELD(order_status , 'Pending', 'Confirmed', 'Accepted_By_Delivery_Agent','Out_For_Delivery', 'Completed','Cancelled','Rejected_By_Delivery_Boy','Rejected_By_Vendor') ASC")
            ->get();

        if (count($ord) > 0) {
            foreach ($ord as $ords) {
                $cart_id = $ords->cart_id;
                $details  =   DB::table('store_orders')
                    ->where('order_cart_id', $cart_id)
                    ->where('store_approval', 1)
                    ->get();


                $data[] = array(
                    'user_address' => $ords->house_no . ',' . $ords->society . ',' . $ords->city . ',' . $ords->landmark . ',' . $ords->state . ',' . $ords->pincode,
                    'cart_id' => $cart_id,
                    'user_name' => $ords->user_name,
                    'user_phone' => $ords->user_phone,
                    'remaining_price' => $ords->rem_price,
                    'order_price' => $ords->total_price,
                    'delivery_charge' => $ords->delivery_charge,
                    'delivery_boy_name' => $ords->boy_name,
                    'delivery_boy_phone' => $ords->boy_phone,
                    'delivery_date' => $ords->delivery_date,
                    'time_slot' => $ords->time_slot,
                    'payment_mode' => $ords->payment_method,
                    'payment_status' => $ords->payment_status,
                    'order_status' => $ords->order_status,
                    'customer_phone' => $ords->user_phone, 'order_details' => $details
                );
            }
        } else {
            $data[] = array('order_details' => 'no orders found');
        }
        return $data;
    }

    public function nextdayorders(Request $request)
    {
        $date = date('Y-m-d');
        $day = 1;
        $next_date = date('Y-m-d', strtotime($date . ' + ' . $day . ' days'));
        $store_id = $request->store_id;
        $store = DB::table('store')
            ->where('store_id', $store_id)
            ->first();

        $ord = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.user_id')
            ->leftJoin('address', 'orders.address_id', '=', 'address.address_id')
            ->leftJoin('delivery_boy', 'orders.dboy_id', '=', 'delivery_boy.dboy_id')
            ->select('orders.cart_id', 'users.user_name', 'users.user_phone', 'orders.delivery_date', 'orders.total_price', 'orders.delivery_charge', 'orders.rem_price', 'orders.payment_status', 'delivery_boy.boy_name', 'delivery_boy.boy_phone', 'orders.time_slot', 'orders.order_status', 'orders.payment_method', 'users.user_phone', 'address.*')
            ->where('orders.store_id', $store_id)
            ->where('payment_method', '!=', NULL)
            ->where('orders.delivery_date', $next_date)
            ->where('orders.order_status', '!=', 'cancelled')
            ->where('orders.order_status', '!=', 'Completed')
            ->orderByRaw("FIELD(order_status , 'Pending', 'Confirmed', 'Out_For_Delivery', 'Completed') ASC")
            ->get();

        if (count($ord) > 0) {
            foreach ($ord as $ords) {
                $cart_id = $ords->cart_id;
                $details  =   DB::table('store_orders')
                    ->where('order_cart_id', $cart_id)
                    ->where('store_approval', 1)
                    ->get();


                $data[] = array(
                    'user_address' => $ords->house_no . ',' . $ords->society . ',' . $ords->city . ',' . $ords->landmark . ',' . $ords->state . ',' . $ords->pincode, 'cart_id' => $cart_id, 'user_name' => $ords->user_name, 'user_phone' => $ords->user_phone,
                    'remaining_price' => $ords->rem_price, 'order_price' => $ords->total_price, 'delivery_boy_name' => $ords->boy_name, 'delivery_boy_phone' => $ords->boy_phone, 'delivery_date' => $ords->delivery_date, 'time_slot' => $ords->time_slot, 'payment_mode' => $ords->payment_method,  'payment_status' => $ords->payment_status, 'order_status' => $ords->order_status, 'customer_phone'    => $ords->user_phone, 'order_details' => $details
                );
            }
        } else {
            $data[] = array('order_details' => 'no orders found');
        }
        return $data;
    }


    public function todayorders(Request $request)
    {
        $date = date('Y-m-d');
        $store_id = $request->store_id;
        $store = DB::table('store')
            ->where('store_id', $store_id)
            ->first();

        $ord = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.user_id')
            ->leftJoin('address', 'orders.address_id', '=', 'address.address_id')
            ->leftJoin('delivery_boy', 'orders.dboy_id', '=', 'delivery_boy.dboy_id')
            ->select('orders.cart_id', 'users.user_name', 'users.user_phone', 'orders.delivery_date', 'orders.total_price', 'orders.delivery_charge', 'orders.rem_price', 'orders.payment_status', 'delivery_boy.boy_name', 'delivery_boy.boy_phone', 'orders.time_slot', 'orders.order_status', 'orders.payment_method', 'users.user_phone', 'address.*')
            ->where('orders.store_id', $store_id)
            ->where('orders.delivery_date', $date)
            ->where('payment_method', '!=', NULL)
            ->where('orders.order_status', '!=', 'cancelled')
            ->where('orders.order_status', '!=', 'Completed')
            ->orderByRaw("FIELD(order_status , 'Pending', 'Confirmed', 'Out_For_Delivery', 'Completed') ASC")
            ->get();

        if (count($ord) > 0) {
            foreach ($ord as $ords) {
                $cart_id = $ords->cart_id;
                $details  =   DB::table('store_orders')
                    ->where('order_cart_id', $cart_id)
                    ->where('store_approval', 1)
                    ->get();


                $data[] = array(
                    'user_address' => $ords->house_no . ',' . $ords->society . ',' . $ords->city . ',' . $ords->landmark . ',' . $ords->state . ',' . $ords->pincode, 'cart_id' => $cart_id, 'user_name' => $ords->user_name, 'user_phone' => $ords->user_phone,
                    'remaining_price' => $ords->rem_price, 'order_price' => $ords->total_price, 'delivery_boy_name' => $ords->boy_name, 'delivery_boy_phone' => $ords->boy_phone, 'delivery_date' => $ords->delivery_date, 'time_slot' => $ords->time_slot, 'payment_mode' => $ords->payment_method, 'payment_status' => $ords->payment_status, 'order_status' => $ords->order_status, 'customer_phone'    => $ords->user_phone, 'order_details' => $details
                );
            }
        } else {
            $data[] = array('order_details' => 'no orders found');
        }
        return $data;
    }

    public function productcancelled(Request $request)
    {
        $id = $request->store_order_id;
        $cart = DB::table('store_orders')
            ->select('order_cart_id', 'varient_id', 'qty')
            ->where('store_order_id', $id)
            ->first();
        $curr = DB::table('currency')
            ->first();
        $cart_id = $cart->order_cart_id;
        $st = DB::table('orders')
            ->where('cart_id', $cart_id)
            ->first();
        $store_id = $st->store_id;
        $var = DB::table('store_orders')
            ->where('order_cart_id', $cart_id)
            ->get();
        $price2 = 0;

        foreach ($var as $h) {
            $varient_id = $h->varient_id;
            $p = DB::table('store_products')
                ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                ->where('store_products.varient_id', $varient_id)
                ->where('store_products.store_id', $store_id)
                ->first();
            $price = $p->price;
            $mrpprice = $p->mrp;
            $order_qty = $h->qty;
            $price2 += $price * $order_qty;
            $unit[] = $p->unit;
            $qty[] = $p->quantity;
            $p_name[] = $p->product_name . "(" . $p->quantity . $p->unit . ")*" . $order_qty;
            $prod_name = implode(',', $p_name);
        }
        $v = DB::table('store_products')
            ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
            ->join('product', 'product_varient.product_id', '=', 'product.product_id')
            ->where('store_products.varient_id', $varient_id)
            ->where('store_products.store_id', $store_id)
            ->first();

        $v_price = $v->price * $cart->qty;
        $ordr = DB::table('orders')
            ->where('cart_id', $cart->order_cart_id)
            ->first();
        $user_id = $ordr->user_id;
        $userwa = DB::table('users')
            ->where('user_id', $user_id)
            ->first();
        if ($ordr->payment_method == 'COD' || $ordr->payment_method == 'Cod' || $ordr->payment_method == 'cod') {
            $newbal = $userwa->wallet;
        } else {
            $newbal = $userwa->wallet + $v_price;
        }
        $orders = DB::table('store_orders')
            ->where('order_cart_id', $cart->order_cart_id)
            ->where('store_approval', 1)
            ->get();

        if (count($orders) == 1 || count($orders) == 0) {
            $email = Session::get('bamaStore');
            $store = DB::table('store')
                ->where('email', $email)
                ->first();


            $cancel = 2;
            $ordupdate = DB::table('orders')
                ->where('cart_id', $cart->order_cart_id)
                ->update([
                    'store_id' => 0,
                    'cancel_by_store' => $cancel
                ]);
            $carte = DB::table('store_orders')
                ->where('order_cart_id', $cart->order_cart_id)
                ->where('store_approval', 0)
                ->get();

            foreach ($carte as $carts) {
                $v1 = DB::table('store_products')
                    ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                    ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                    ->where('store_products.varient_id', $varient_id)
                    ->where('store_products.store_id', $store_id)
                    ->first();

                $v_price1 = $v1->price * $carts->qty;
                $ordr1 = DB::table('orders')
                    ->where('cart_id', $carts->order_cart_id)
                    ->first();
                $user_id1 = $ordr1->user_id;
                $userwa1 = DB::table('users')
                    ->where('user_id', $user_id1)
                    ->first();
                $newbal1 = $userwa1->wallet - $v_price1;
                $userwalletupdate = DB::table('users')
                    ->where('user_id', $user_id1)
                    ->update(['wallet' => $newbal1]);
            }

            $cart_update = DB::table('store_orders')
                ->where('order_cart_id', $cart->order_cart_id)
                ->update(['store_approval' => 1]);
            $data[] = array('result' => 'order cancelled successfully');
        } else {
            $cancel_product = DB::table('store_orders')
                ->where('store_order_id', $id)
                ->update(['store_approval' => 0]);
            $userwallet = DB::table('users')
                ->where('user_id', $user_id)
                ->update(['wallet' => $newbal]);
            $data[] = array('result' => 'product cancelled successfully');
        }
        return $data;
    }

    public function order_rejected(Request $request)
    {
        $cart_id = $request->cart_id;
        $store_id = $request->store_id;

        $order = DB::table('orders')
            ->where('cart_id', $cart_id)
            ->first();
        if($order->order_status != 'Pending'){
            $data[] = array('status'=>0,'result' => 'Order already in process');
            return $data;
        }
        
        $var = DB::table('store_orders')
            ->where('order_cart_id', $cart_id)
            ->where('store_approval', 1)
            ->get();
                    
        // Auto Refund Code
        /* $user_id1 = $order->user_id;
        $user = DB::table('users')
            ->where('user_id', $user_id1)
            ->first();

        $refund = 0;
        if ($order->payment_method == 'COD' || $order->payment_method == 'Cod' || $order->payment_method == 'cod') {
            $refund = $order->paid_by_wallet;
            $newbal1 = $user->wallet + $order->paid_by_wallet;
        } else {
            if ($order->payment_status == 'success') {
                $refund = $order->rem_price + $order->paid_by_wallet;
                $newbal1 = $user->wallet + $order->rem_price + $order->paid_by_wallet;
            } else {
                $newbal1 = $user->wallet;
            }
        }
        $refundEntry = WalletHistory::where('cart_id',$order->cart_id)
                        ->where('trans_type','refund')->first();

        if($refund > 0 && !$refundEntry){
            $userwalletupdate = DB::table('users')
                ->where('user_id', $user_id1)
                ->update(['wallet' => $newbal1]);

            $walletHistory = new WalletHistory();
            $walletHistory->user_id = $user_id1;
            $walletHistory->cart_id = $order->cart_id;
            $walletHistory->trans_type = 'refund';
            $walletHistory->amount = $refund;
            $walletHistory->status = 'success';
            $walletHistory->save();
        } */


        $price2 = 0;
        foreach ($var as $h) {
            $varient_id = $h->varient_id;
            $p = DB::table('store_products')
                ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                ->where('store_products.varient_id', $varient_id)
                ->where('store_products.store_id', $store_id)
                ->first();
            $price = $p->price;
            $order_qty = $h->qty;
            $price2 += $price * $order_qty;
            $unit[] = $p->unit;
            $qty[] = $p->quantity;
            $p_name[] = $p->product_name . "(" . $p->quantity . $p->unit . ")*" . $order_qty;
            $prod_name = implode(',', $p_name);
        }

    
        $cancel = 2;
        $ordupdate = DB::table('orders')
            ->where('cart_id', $cart_id)
            ->update(['cancel_by_store' => $cancel, 'order_status' => 'Rejected_By_Vendor']);

        $carte = DB::table('store_orders')
            ->where('order_cart_id', $cart_id)
            ->where('store_approval', 0)
            ->get();
        $user = DB::table('users')
            ->where('user_id', $order->user_id)
            ->first();
        $orderplacedmsg = $this->orderreject($order->cart_id, $prod_name, $price2, $user->user_phone, 'Vendor');
        $data[] = array('status'=>1,'result' => 'Order Rejected successfully');
        return $data;
    }
}
