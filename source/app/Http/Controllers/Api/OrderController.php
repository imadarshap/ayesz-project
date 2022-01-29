<?php

namespace App\Http\Controllers\Api;

use App\DeliveryRating;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Traits\SendMail;
use App\Traits\SendSms;
use DateTime;
use App\Helper\Helper;
use App\OrderRating;
use App\WalletHistory;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;

class OrderController extends Controller
{
    use SendMail;
    use SendSms;

    public function createOrder(Request $request)
    {
        $current = Carbon::now();
        $data = $request->order_array;
        $data_array = json_decode($data);
        $user_id = $request->user_id;
        $delivery_date = $request->delivery_date;
        $store_id = $request->store_id;
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $val = "";
        for ($i = 0; $i < 4; $i++) {
            $val .= $chars[mt_rand(0, strlen($chars) - 1)];
        }

        $chars2 = "0123456789";
        $val2 = "";
        for ($i = 0; $i < 2; $i++) {
            $val2 .= $chars2[mt_rand(0, strlen($chars2) - 1)];
        }
        $cr  = substr(md5(microtime()), rand(0, 26), 2);
        $cart_id = $val . $val2 . $cr;
        $ar = DB::table('address')
            ->select('society', 'city', 'lat', 'lng', 'address_id')
            ->where('user_id', $user_id)
            ->where('select_status', 1)
            ->first();
        if (!$ar) {
            $message = array('status' => '0', 'message' => 'Select any Address');
            return $message;
        }
        $created_at = Carbon::now();
        $user_id = $request->user_id;
        $price2 = 0;
        $price5 = 0;
        $user_data = DB::table('users')
            ->select('user_phone', 'wallet')
            ->where('user_id', $user_id)
            ->first();
        $user_phone = $user_data->user_phone;


        foreach ($data_array as $h) {
            $varient_id = $h->varient_id;
            $p =  DB::table('store_products')
                ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                ->Leftjoin('deal_product', 'product_varient.varient_id', '=', 'deal_product.varient_id')
                ->where('product_varient.varient_id', $varient_id)
                ->where('store_products.store_id', $store_id)
                ->first();
            if ($p->deal_price != NULL &&  $p->valid_from < $current && $p->valid_to > $current) {
                $price = $p->deal_price;
            } else {
                $price = $p->price;
            }

            $mrpprice = $p->mrp;
            $order_qty = $h->qty;
            $price2 += $price * $order_qty;
            $price5 += $mrpprice * $order_qty;
            $unit[] = $p->unit;
            $qty[] = $p->quantity;
            $p_name[] = $p->product_name . "(" . $p->quantity . $p->unit . ")*" . $order_qty;
            $prod_name = implode(',', $p_name);
        }

        foreach ($data_array as $h) {
            $varient_id = $h->varient_id;
            $p =  DB::table('store_products')
                ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                ->Leftjoin('deal_product', 'product_varient.varient_id', '=', 'deal_product.varient_id')
                ->where('product_varient.varient_id', $varient_id)
                ->where('store_products.store_id', $store_id)
                ->first();
            if ($p->deal_price != NULL &&  $p->valid_from < $current && $p->valid_to > $current) {
                $price = $p->deal_price;
            } else {
                $price = $p->price;
            }
            $mrp = $p->mrp;
            $order_qty = $h->qty;
            $price1 = $price * $order_qty;
            $total_mrp = $mrp * $order_qty;
            $order_qty = $h->qty;
            $p = DB::table('store_products')
                ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                ->Leftjoin('deal_product', 'product_varient.varient_id', '=', 'deal_product.varient_id')
                ->where('product_varient.varient_id', $varient_id)
                ->where('store_products.store_id', $store_id)
                ->first();

            $n = $p->product_name;

            $insert = DB::table('store_orders')
                ->insertGetId([
                    'varient_id' => $varient_id,
                    'qty' => $order_qty,
                    'product_name' => $n,
                    'varient_image' => $p->varient_image,
                    'quantity' => $p->quantity,
                    'unit' => $p->unit,
                    'total_mrp' => $total_mrp,
                    'order_cart_id' => $cart_id,
                    'order_date' => $created_at,
                    'price' => $price1
                ]);
        }
        $charge = $this->get_delivery_charge($user_id, $store_id, $data_array);

        if ($insert) {
            $data = [
                'cart_id' => $cart_id,
                'total_price' => $price2 + $charge,
                'price_without_delivery' => $price2,
                'total_products_mrp' => $price5,
                'delivery_charge' => $charge,
                'user_id' => $user_id,
                'store_id' => $store_id,
                'rem_price' => $price2 + $charge,
                'order_date' => $created_at,
                'delivery_date' => $delivery_date,
                'time_slot' => date('H:i:s'),
                'address_id' => $ar->address_id
            ];
            $total_price = $price2 + $charge;
            $payble_price = $total_price;
            if(!empty($request->coupon_code)){
                $currentdate = Carbon::now();
                $coupon_code = $request->coupon_code;

                $coupon = DB::table('coupon')
                    ->where('coupon_code', $coupon_code)
                    ->where('cart_value', '<=', $price2)
                    ->where('start_date', '<=', $currentdate)
                    ->where('end_date', '>=', $currentdate)
                    ->first();
                if($coupon){
                    $check_uses = DB::table('orders')
                        ->where('coupon_id', $coupon->coupon_id)
                        ->where('user_id', $user_id)
                        ->where('payment_method','!=',NULL)
                        ->whereIn('order_status',['Pending','Confirmed','Accepted_By_Delivery_Agent','Out_For_Delivery','Completed'])
                        ->count();

                    if ($coupon->uses_restriction > $check_uses) {
                        $am = $coupon->amount;
                        $type = $coupon->type;
                        if ($type == '%' || $type == 'Percentage' || $type == 'percentage') {
                            $per = ($price2 * $am) / 100;
                            if($coupon->max_discount != null && $coupon->max_discount < $per){
                                $per = $coupon->max_discount;
                            }
                            $rem_price = $total_price - $per;
                        } else {
                            $per = $am;
                            $rem_price = $total_price - $am;
                        }
                        $payble_price = $rem_price;
                        $data['rem_price'] = $rem_price;
                        $data['coupon_discount'] = $per;
                        $data['coupon_id'] = $coupon->coupon_id;
                    }
                }
            }

            if(!empty($request->payment_mode) && $request->payment_mode == 'razorpay'){
                if(!empty($request->wallet) && $request->wallet=='true'){
                    if($user_data->wallet > 0 && $user_data->wallet<$payble_price){
                       $payble_price = $payble_price - $user_data->wallet;
                    }else if($user_data->wallet > $payble_price){
                        $message = array('status' => '0', 'message' => 'Invalid Payment Mode', 'data' => []);
                        return $message;
                    }
                }
                $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
                $orderData = [
                    'receipt'         => "Order #".$cart_id,
                    'amount'          => $payble_price * 100,
                    'currency'        => 'INR'
                ];
                $razorpayOrder = $api->order->create($orderData);
                $data['payment_order_id'] = $razorpayOrder->id;
            }

            $oo = DB::table('orders')
                ->insertGetId($data);

            $ordersuccessed = DB::table('orders')
                ->where('order_id', $oo)
                ->first();
            $message = array('status' => '1', 'message' => 'Proceed to payment', 'data' => $ordersuccessed);
            return $message;
        } else {
            $message = array('status' => '0', 'message' => 'insertion failed', 'data' => []);
            return $message;
        }
    }

    public function placeOrder(Request $request)
    {
        $cart_id = $request->cart_id;
        $payment_method = $request->payment_method;
        $payment_status = $request->payment_status;
        $wallet = $request->wallet;
        $orderr = DB::table('orders')
            ->where('cart_id', $cart_id)
            ->first();
        if(empty($orderr)){
            $message = array('status'=>'0', 'message'=>'Invalid Order');
            return $message;
        }
        $store_id = $orderr->store_id;
        $user_id = $orderr->user_id;
        $delivery_date = $orderr->delivery_date;
        $time_slot = $orderr->time_slot;

        if($orderr->order_status=='Completed'){
            $message = array('status'=>'1', 'message'=>'Order is already Completed');
            return $message;
        }

        if($orderr->payment_method !=null){
            return array('status'=>'1', 'message'=>'Order is already in process');
        }

        $var = DB::table('store_orders')
            ->where('order_cart_id', $cart_id)
            ->get();
        $price2 = $orderr->rem_price;
        $user_data = DB::table('users')
            ->select('user_phone', 'wallet')
            ->where('user_id', $user_id)
            ->first();
        $user_phone = $user_data->user_phone;
        foreach ($var as $h) {
            $varient_id = $h->varient_id;
            $p = DB::table('store_orders')
                ->where('order_cart_id', $cart_id)
                ->where('varient_id', $varient_id)
                ->first();
            $price = $p->price;
            $order_qty = $h->qty;
            $unit[] = $p->unit;
            $qty[] = $p->quantity;
            $p_name[] = $p->product_name . "(" . $p->quantity . $p->unit . ")*" . $order_qty;
            $prod_name = implode(',', $p_name);
        }

        if(!empty($request->payment_id)){
            $paymentId = $request->payment_id;
            $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
            $rp_order = $api->order->fetch($orderr->payment_order_id)->payments();
            if(empty($rp_order)){
                return array('status'=>'0', 'message'=>'Invalid Payment');
            }
            $payment_method = $rp_order->items[0]->method;
            $payble_price = $orderr->total_price-$orderr->coupon_discount;
            if($wallet=='yes'){
                if($user_data->wallet > 0 && $user_data->wallet<$payble_price){
                    $payble_price = $payble_price - $user_data->wallet;
                }else if($user_data->wallet > $payble_price){
                    $message = array('status' => '0', 'message' => 'Invalid Payment Mode');
                    return $message;
                }else if($user_data->wallet == 0){
                    $message = array('status' => '0', 'message' => 'Invalid Payment Mode');
                    return $message;
                }
            }

            if($rp_order->items[0]->status != 'failed' && $payble_price*100 == $rp_order->items[0]->amount){
                DB::table('orders')
                ->where('cart_id', $cart_id)
                ->update([
                    'payment_id' => $paymentId,
                ]);
            }else{
                $message = array('status'=>'0', 'message'=>'Payment is failed');
                return $message;
            }
        }

        $charge = 0;
        $order_price = $price2;
        if ($payment_method == 'COD' || $payment_method == 'cod') {
            $walletamt = 0;

            $payment_status = "COD";
            if ($wallet == 'yes' || $wallet == 'Yes' || $wallet == 'YES') {
                if ($user_data->wallet >= $order_price) {
                    $rem_amount = 0;
                    $walletamt = $order_price;
                    $rem_wallet = $user_data->wallet - $order_price;

                    $walletOrder = new WalletHistory();
                    $walletOrder->user_id = $user_id;
                    $walletOrder->cart_id = $cart_id;
                    $walletOrder->trans_type = 'order';
                    $walletOrder->amount = $order_price;
                    $walletOrder->status = 'success';
                    $walletOrder->save();

                    $walupdate = DB::table('users')
                        ->where('user_id', $user_id)
                        ->update(['wallet' => $rem_wallet]);

                    $payment_status = "success";
                    $payment_method = "wallet";
                } else {

                    $rem_amount = $order_price - $user_data->wallet;
                    $walletamt = $user_data->wallet;
                    $rem_wallet = 0;

                    $walletOrder = new WalletHistory();
                    $walletOrder->user_id = $user_id;
                    $walletOrder->cart_id = $cart_id;
                    $walletOrder->trans_type = 'order';
                    $walletOrder->amount = $walletamt;
                    $walletOrder->status = 'success';
                    $walletOrder->save();

                    $walupdate = DB::table('users')
                        ->where('user_id', $user_id)
                        ->update(['wallet' => $rem_wallet]);
                }
            } else {
                $rem_amount =  $order_price;
                $walletamt = 0;
            }

            $oo = DB::table('orders')
                ->where('cart_id', $cart_id)
                ->update([
                    'paid_by_wallet' => $walletamt,
                    'rem_price' => $rem_amount,
                    'payment_status' => $payment_status,
                    'payment_method' => $payment_method
                ]);

            $sms = DB::table('notificationby')
                ->select('sms')
                ->where('user_id', $user_id)
                ->first();
            
            $sms_status = (!empty($sms)) ? $sms->sms:0;

            if ($sms_status == 1) {
                $orderplacedmsg = $this->ordersuccessfull($cart_id, $prod_name, $price2, $delivery_date, $time_slot, $user_phone);
            }
            /////send mail
            $email = DB::table('notificationby')
                ->select('email', 'app')
                ->where('user_id', $user_id)
                ->first();
            $q = DB::table('users')
                ->select('user_email', 'user_name')
                ->where('user_id', $user_id)
                ->first();

            $user_email = $q->user_email;

            $user_name = $q->user_name;
            $email_status = (!empty($email))?$email->email:0;
            if ($email_status == 1) {
                $codorderplaced = $this->codorderplacedMail($cart_id, $prod_name, $price2, $delivery_date, $time_slot, $user_email, $user_name);
            }
            if (!empty($email) && $email->app == 1) {
                $notification_title = "WooHoo! Your Order is Placed";
                $notification_text = "Order Successfully Placed: Your order id #" . $cart_id . " contains of " . $prod_name . " of price rs " . $price2 . " is placed Successfully.You can expect your item(s) will be delivered on " . $delivery_date;

                $date = date('d-m-Y');


                $getDevice = DB::table('users')
                    ->where('user_id', $user_id)
                    ->select('device_id')
                    ->first();
                $created_at = Carbon::now();

                if ($getDevice) {


                    $getFcm = DB::table('fcm')
                        ->where('id', '1')
                        ->first();

                    $getFcmKey = $getFcm->server_key;
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
                        'Authorization: key=' . $getFcmKey,
                        'Content-Type: application/json'
                    ];

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $fcmUrl);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
                    $result = curl_exec($ch);
                    curl_close($ch);


                    $dd = DB::table('user_notification')
                        ->insert([
                            'user_id' => $user_id,
                            'noti_title' => $notification_title,
                            'noti_message' => $notification_text
                        ]);

                    $results = json_decode($result);
                }
            }
            $orderr1 = DB::table('orders')
                ->where('cart_id', $cart_id)
                ->first();

            ///////send notification to store//////

            $notification_title = "New Order";
            $notification_text = "you got an order cart id #" . $cart_id . " contains of " . $prod_name . " of price rs " . $price2 . ". It will have to delivered on " . $delivery_date;

            $date = date('d-m-Y');
            $getUser = DB::table('store')
                ->get();

            $getDevice = DB::table('store')
                ->where('store_id', $store_id)
                ->select('device_id')
                ->first();
            $created_at = Carbon::now();

            if ($getDevice) {


                $getFcm = DB::table('fcm')
                    ->where('id', '1')
                    ->first();

                $getFcmKey = $getFcm->store_server_key;
                $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
                $token = $getDevice->device_id;

                $body = json_encode(array('message' => $notification_text, 'cart_id' => $cart_id));
                $notification = [
                    'title' => "New Order",
                    'body' => "You Got a new order",
                    'android_channel_id' => 'AyesZ_Vendor_Notifications',
                    'sound' => "default",
                ];

                $extraNotificationData = [
                    "store_id" => $store_id,
                    "cart_id" => $cart_id,
                    'title' => "New Order",
                    'body' => $notification_text,
                ];

                $fcmNotification = [
                    'to'        => $token,
                    'data' => $extraNotificationData,
                    'content_available' => false, //important for iOS
                    'priority' => "high",
                    // 'time_to_live' => 5000,
                    'requireInteraction' => true,
                    'actions' => [
                        'action' => "accept",
                        'title' => "Accept"
                    ], [
                        'action' => "reject",
                        'title' => "Reject"
                    ]
                ];

                $headers = [
                    'Authorization: key=' . $getFcmKey,
                    'Content-Type: application/json'
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $fcmUrl);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
                $result = curl_exec($ch);
                curl_close($ch);

                ///////send notification to store//////

                $dd = DB::table('store_notification')
                    ->insert([
                        'store_id' => $store_id,
                        'not_title' => $notification_title,
                        'not_message' => $notification_text
                    ]);

                $results = json_decode($result);
            }

            ////rewards earned////
            $checkre = DB::table('reward_points')
                ->where('min_cart_value', '<=', $price2)
                ->orderBy('min_cart_value', 'desc')
                ->first();
            if ($checkre) {
                $reward_point = $checkre->reward_point;

                $inreward = DB::table('users')
                    ->where('user_id', $user_id)
                    ->update(['rewards' => $reward_point]);

                $cartreward = DB::table('cart_rewards')
                    ->insert(['cart_id' => $cart_id, 'rewards' => $reward_point, 'user_id' => $user_id]);
            }

            $message = array('status' => '1', 'message' => 'Order Placed successfully', 'data' => $orderr1);
            return $message;
        } else {
            $walletamt = 0;
            $order_price = $price2 + $charge;
            if ($request->wallet == 'yes' || $request->wallet == 'Yes' || $request->wallet == 'YES') {
                if ($user_data->wallet >= $order_price) {
                    $rem_amount = 0;
                    $walletamt = $order_price;
                    $rem_wallet = $user_data->wallet - $order_price;
                    $walletOrder = new WalletHistory();
                    $walletOrder->user_id = $user_id;
                    $walletOrder->cart_id = $cart_id;
                    $walletOrder->trans_type = 'order';
                    $walletOrder->amount = $order_price;
                    $walletOrder->status = 'success';
                    $walletOrder->save();

                    $walupdate = DB::table('users')
                        ->where('user_id', $user_id)
                        ->update(['wallet' => $rem_wallet]);
                    $payment_status = "success";
                    $payment_method = "wallet";
                } else {

                    $rem_amount =  $order_price - $user_data->wallet;
                    $walletamt = $user_data->wallet;
                    $rem_wallet = 0;
                    
                    $walletOrder = new WalletHistory();
                    $walletOrder->user_id = $user_id;
                    $walletOrder->cart_id = $cart_id;
                    $walletOrder->trans_type = 'order';
                    $walletOrder->amount = $walletamt;
                    $walletOrder->status = 'success';
                    $walletOrder->save();

                    $walupdate = DB::table('users')
                        ->where('user_id', $user_id)
                        ->update(['wallet' => $rem_wallet]);
                }
            } else {
                $rem_amount =  $order_price;
                $walletamt = 0;
            }
            if ($payment_status == 'success') {
                $oo = DB::table('orders')
                    ->where('cart_id', $cart_id)
                    ->update([
                        'paid_by_wallet' => $walletamt,
                        'rem_price' => $rem_amount,
                        'payment_method' => $payment_method,
                        'payment_status' => 'success'
                    ]);
                $sms = DB::table('notificationby')
                    ->select('sms')
                    ->where('user_id', $user_id)
                    ->first();
                $sms_status = (!empty($sms)) ? $sms->sms:0;
                if ($sms_status == 1) {
                    $codorderplaced = $this->ordersuccessfull($cart_id, $prod_name, $price2, $delivery_date, $time_slot, $user_phone);
                }
                /////send mail
                $email = DB::table('notificationby')
                    ->select('email', 'app')
                    ->where('user_id', $user_id)
                    ->first();
                $email_status = (!empty($email))?$email->email:0;
                $q = DB::table('users')
                    ->select('user_email', 'user_name')
                    ->where('user_id', $user_id)
                    ->first();
                $user_email = $q->user_email;
                $user_name = $q->user_name;
                if ($email_status == 1) {
                    $orderplaced = $this->orderplacedMail($cart_id, $prod_name, $price2, $delivery_date, $time_slot, $user_email, $user_name);
                }
                if (!empty($email) && $email->app == 1) {
                    $notification_title = "WooHoo! Your Order is Placed";
                    $notification_text = "Order Successfully Placed: Your order id #" . $cart_id . " contains of " . $prod_name . " of price rs " . $price2 . " is placed Successfully.You can expect your item(s) will be delivered on " . $delivery_date;

                    $date = date('d-m-Y');


                    $getDevice = DB::table('users')
                        ->where('user_id', $user_id)
                        ->select('device_id')
                        ->first();
                    $created_at = Carbon::now();

                    if ($getDevice) {
                        $getFcm = DB::table('fcm')
                            ->where('id', '1')
                            ->first();

                        $getFcmKey = $getFcm->server_key;
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
                            'Authorization: key=' . $getFcmKey,
                            'Content-Type: application/json'
                        ];

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $fcmUrl);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
                        $result = curl_exec($ch);
                        curl_close($ch);


                        $dd = DB::table('user_notification')
                            ->insert([
                                'user_id' => $user_id,
                                'noti_title' => $notification_title,
                                'noti_message' => $notification_text
                            ]);

                        $results = json_decode($result);
                    }
                }
                $orderr1 = DB::table('orders')
                    ->where('cart_id', $cart_id)
                    ->first();

                ///////send notification to store//////

                $notification_title = "WooHoo ! You Got a New Order";
                $notification_text = "you got an order cart id #" . $cart_id . " contains of " . $prod_name . " of price rs " . $price2 . ". It will have to delivered on " . $delivery_date;

                $date = date('d-m-Y');
                $getUser = DB::table('store')
                    ->get();

                $getDevice = DB::table('store')
                    ->where('store_id', $store_id)
                    ->select('device_id')
                    ->first();
                $created_at = Carbon::now();

                if ($getDevice) {
                    
                    $getFcm = DB::table('fcm')
                    ->where('id', '1')
                    ->first();

                    $getFcmKey = $getFcm->store_server_key;
                    $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
                    $token = $getDevice->device_id;
    
                    $body = json_encode(array('message' => $notification_text, 'cart_id' => $cart_id));
                    $notification = [
                        'title' => "New Order",
                        'body' => "You Got a new order",
                        'android_channel_id' => 'AyesZ_Vendor_Notifications',
                        'sound' => "default",
                    ];
    
                    $extraNotificationData = [
                        "store_id" => $store_id,
                        "cart_id" => $cart_id,
                        'title' => "New Order",
                        'body' => $notification_text,
                    ];
    
                    $fcmNotification = [
                        'to'        => $token,
                        'data' => $extraNotificationData,
                        'content_available' => false, //important for iOS
                        'priority' => "high",
                        // 'time_to_live' => 5000,
                        'requireInteraction' => true,
                        'actions' => [
                            'action' => "accept",
                            'title' => "Accept"
                        ], [
                            'action' => "reject",
                            'title' => "Reject"
                        ]
                    ];

                    $headers = [
                        'Authorization: key=' . $getFcmKey,
                        'Content-Type: application/json'
                    ];

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $fcmUrl);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
                    $result = curl_exec($ch);
                    curl_close($ch);

                    ///////send notification to store//////

                    $dd = DB::table('store_notification')
                        ->insert([
                            'store_id' => $store_id,
                            'not_title' => $notification_title,
                            'not_message' => $notification_text
                        ]);

                    $results = json_decode($result);
                }
                ////rewards earned////
                $checkre = DB::table('reward_points')
                    ->where('min_cart_value', '<=', $price2)
                    ->orderBy('min_cart_value', 'desc')
                    ->first();
                if ($checkre) {
                    $reward_point = $checkre->reward_point;

                    $inreward = DB::table('users')
                        ->where('user_id', $user_id)
                        ->update(['rewards' => $reward_point]);

                    $cartreward = DB::table('cart_rewards')
                        ->insert(['cart_id' => $cart_id, 'rewards' => $reward_point, 'user_id' => $user_id]);
                }
                $message = array('status' => '2', 'message' => 'Order Placed successfully', 'data' => $orderr1);
                return $message;
            } else {
                $oo = DB::table('orders')
                    ->where('cart_id', $cart_id)
                    ->update([
                        'paid_by_wallet' => 0,
                        'rem_price' => $rem_amount,
                        'payment_method' => NULL,
                        'payment_status' => 'failed'
                    ]);
                $message = array('status' => '0', 'message' => 'Payment Failed');
                return $message;
            }
        }
    }

    public function order(Request $request)
    {
        $current = Carbon::now();
        $data = $request->order_array;
        $data_array = json_decode($data);
        $user_id = $request->user_id;
        $delivery_date = $request->delivery_date;
        $time_slot = $request->time_slot;
        $store_id = $request->store_id;
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $val = "";
        for ($i = 0; $i < 4; $i++) {
            $val .= $chars[mt_rand(0, strlen($chars) - 1)];
        }

        $chars2 = "0123456789";
        $val2 = "";
        for ($i = 0; $i < 2; $i++) {
            $val2 .= $chars2[mt_rand(0, strlen($chars2) - 1)];
        }
        $cr  = substr(md5(microtime()), rand(0, 26), 2);
        $cart_id = $val . $val2 . $cr;
        $ar = DB::table('address')
            ->select('society', 'city', 'lat', 'lng', 'address_id')
            ->where('user_id', $user_id)
            ->where('select_status', 1)
            ->first();
        if (!$ar) {
            $message = array('status' => '0', 'message' => 'Select any Address');
            return $message;
        }
        $created_at = Carbon::now();
        $user_id = $request->user_id;
        $price2 = 0;
        $price5 = 0;
        $ph = DB::table('users')
            ->select('user_phone', 'wallet')
            ->where('user_id', $user_id)
            ->first();
        $user_phone = $ph->user_phone;


        foreach ($data_array as $h) {
            $varient_id = $h->varient_id;
            $p =  DB::table('store_products')
                ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                ->Leftjoin('deal_product', 'product_varient.varient_id', '=', 'deal_product.varient_id')
                ->where('product_varient.varient_id', $varient_id)
                ->where('store_products.store_id', $store_id)
                ->first();
            if ($p->deal_price != NULL &&  $p->valid_from < $current && $p->valid_to > $current) {
                $price = $p->deal_price;
            } else {
                $price = $p->price;
            }

            $mrpprice = $p->mrp;
            $order_qty = $h->qty;
            $price2 += $price * $order_qty;
            $price5 += $mrpprice * $order_qty;
            $unit[] = $p->unit;
            $qty[] = $p->quantity;
            $p_name[] = $p->product_name . "(" . $p->quantity . $p->unit . ")*" . $order_qty;
            $prod_name = implode(',', $p_name);
        }

        foreach ($data_array as $h) {
            $varient_id = $h->varient_id;
            $p =  DB::table('store_products')
                ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                ->Leftjoin('deal_product', 'product_varient.varient_id', '=', 'deal_product.varient_id')
                ->where('product_varient.varient_id', $varient_id)
                ->where('store_products.store_id', $store_id)
                ->first();
            if ($p->deal_price != NULL &&  $p->valid_from < $current && $p->valid_to > $current) {
                $price = $p->deal_price;
            } else {
                $price = $p->price;
            }
            $mrp = $p->mrp;
            $order_qty = $h->qty;
            $price1 = $price * $order_qty;
            $total_mrp = $mrp * $order_qty;
            $order_qty = $h->qty;
            $p = DB::table('store_products')
                ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                ->Leftjoin('deal_product', 'product_varient.varient_id', '=', 'deal_product.varient_id')
                ->where('product_varient.varient_id', $varient_id)
                ->where('store_products.store_id', $store_id)
                ->first();

            $n = $p->product_name;


            $insert = DB::table('store_orders')
                ->insertGetId([
                    'varient_id' => $varient_id,
                    'qty' => $order_qty,
                    'product_name' => $n,
                    'varient_image' => $p->varient_image,
                    'quantity' => $p->quantity,
                    'unit' => $p->unit,
                    'total_mrp' => $total_mrp,
                    'order_cart_id' => $cart_id,
                    'order_date' => $created_at,
                    'price' => $price1
                ]);
        }
        $charge = $this->get_delivery_charge($user_id, $store_id, $data_array);

        if ($insert) {
            $oo = DB::table('orders')
                ->insertGetId([
                    'cart_id' => $cart_id,
                    'total_price' => $price2 + $charge,
                    'price_without_delivery' => $price2,
                    'total_products_mrp' => $price5,
                    'delivery_charge' => $charge,
                    'user_id' => $user_id,
                    'store_id' => $store_id,
                    'rem_price' => $price2 + $charge,
                    'order_date' => $created_at,
                    'delivery_date' => $delivery_date,
                    'time_slot' => date('H:i:s'),
                    'address_id' => $ar->address_id
                ]);

            $ordersuccessed = DB::table('orders')
                ->where('order_id', $oo)
                ->first();
            $message = array('status' => '1', 'message' => 'Proceed to payment', 'data' => $ordersuccessed);
            return $message;
        } else {
            $message = array('status' => '0', 'message' => 'insertion failed', 'data' => []);
            return $message;
        }
    }

    public function checkout(Request $request)
    {
        $cart_id = $request->cart_id;
        $payment_method = $request->payment_method;
        $payment_status = $request->payment_status;
        $wallet = $request->wallet;
        $orderr = DB::table('orders')
            ->where('cart_id', $cart_id)
            ->first();
        $store_id = $orderr->store_id;
        $user_id = $orderr->user_id;
        $delivery_date = $orderr->delivery_date;
        $time_slot = $orderr->time_slot;
        
        if($orderr->order_status=='Completed'){
            $message = array('status'=>'0', 'message'=>'Order is already Completed');
            return $message;
        }

        $var = DB::table('store_orders')
            ->where('order_cart_id', $cart_id)
            ->get();
        $price2 = $orderr->rem_price;
        $ph = DB::table('users')
            ->select('user_phone', 'wallet')
            ->where('user_id', $user_id)
            ->first();
        $user_phone = $ph->user_phone;
        foreach ($var as $h) {
            $varient_id = $h->varient_id;
            $p = DB::table('store_orders')
                ->where('order_cart_id', $cart_id)
                ->where('varient_id', $varient_id)
                ->first();
            $price = $p->price;
            $order_qty = $h->qty;
            $unit[] = $p->unit;
            $qty[] = $p->quantity;
            $p_name[] = $p->product_name . "(" . $p->quantity . $p->unit . ")*" . $order_qty;
            $prod_name = implode(',', $p_name);
        }
        $charge = 0;
        $prii = $price2;
        if ($payment_method == 'COD' || $payment_method == 'cod') {
            $walletamt = 0;

            $payment_status = "COD";
            if ($wallet == 'yes' || $wallet == 'Yes' || $wallet == 'YES') {
                if ($ph->wallet >= $prii) {
                    $rem_amount = 0;
                    $walletamt = $prii;
                    $rem_wallet = $ph->wallet - $prii;
                    $walupdate = DB::table('users')
                        ->where('user_id', $user_id)
                        ->update(['wallet' => $rem_wallet]);
                    $payment_status = "success";
                    $payment_method = "wallet";
                } else {

                    $rem_amount = $prii - $ph->wallet;
                    $walletamt = $ph->wallet;
                    $rem_wallet = 0;
                    $walupdate = DB::table('users')
                        ->where('user_id', $user_id)
                        ->update(['wallet' => $rem_wallet]);
                }
            } else {
                $rem_amount =  $prii;
                $walletamt = 0;
            }

            $oo = DB::table('orders')
                ->where('cart_id', $cart_id)
                ->update([
                    'paid_by_wallet' => $walletamt,
                    'rem_price' => $rem_amount,
                    'payment_status' => $payment_status,
                    'payment_method' => $payment_method
                ]);

            $sms = DB::table('notificationby')
                ->select('sms')
                ->where('user_id', $user_id)
                ->first();
            $sms_status = (!empty($sms)) ? $sms->sms:0;

            if ($sms_status == 1) {
                $orderplacedmsg = $this->ordersuccessfull($cart_id, $prod_name, $price2, $delivery_date, $time_slot, $user_phone);
            }
            /////send mail
            $email = DB::table('notificationby')
                ->select('email', 'app')
                ->where('user_id', $user_id)
                ->first();
            $q = DB::table('users')
                ->select('user_email', 'user_name')
                ->where('user_id', $user_id)
                ->first();
            $user_email = $q->user_email;

            $user_name = $q->user_name;
            $email_status = !empty($email)?$email->email:0;
            if ($email_status == 1) {
                $codorderplaced = $this->codorderplacedMail($cart_id, $prod_name, $price2, $delivery_date, $time_slot, $user_email, $user_name);
            }

            $app_status = !empty($email)?$email->app:0;
            if ($app_status == 1) {
                $notification_title = "WooHoo! Your Order is Placed";
                $notification_text = "Order Successfully Placed: Your order id #" . $cart_id . " contains of " . $prod_name . " of price rs " . $price2 . " is placed Successfully.You can expect your item(s) will be delivered on " . $delivery_date;

                $date = date('d-m-Y');


                $getDevice = DB::table('users')
                    ->where('user_id', $user_id)
                    ->select('device_id')
                    ->first();
                $created_at = Carbon::now();

                if ($getDevice) {


                    $getFcm = DB::table('fcm')
                        ->where('id', '1')
                        ->first();

                    $getFcmKey = $getFcm->server_key;
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
                        'Authorization: key=' . $getFcmKey,
                        'Content-Type: application/json'
                    ];

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $fcmUrl);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
                    $result = curl_exec($ch);
                    curl_close($ch);


                    $dd = DB::table('user_notification')
                        ->insert([
                            'user_id' => $user_id,
                            'noti_title' => $notification_title,
                            'noti_message' => $notification_text
                        ]);

                    $results = json_decode($result);
                }
            }
            $orderr1 = DB::table('orders')
                ->where('cart_id', $cart_id)
                ->first();

            ///////send notification to store//////

            $notification_title = "New Order";
            $notification_text = "you got an order cart id #" . $cart_id . " contains of " . $prod_name . " of price rs " . $price2 . ". It will have to delivered on " . $delivery_date;

            $date = date('d-m-Y');
            $getUser = DB::table('store')
                ->get();

            $getDevice = DB::table('store')
                ->where('store_id', $store_id)
                ->select('device_id')
                ->first();
            $created_at = Carbon::now();

            if ($getDevice) {


                $getFcm = DB::table('fcm')
                    ->where('id', '1')
                    ->first();

                $getFcmKey = $getFcm->store_server_key;
                $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
                $token = $getDevice->device_id;

                $body = json_encode(array('message' => $notification_text, 'cart_id' => $cart_id));
                $notification = [
                    'title' => "New Order",
                    'body' => "You Got a new order",
                    'android_channel_id' => 'AyesZ_Vendor_Notifications',
                    'sound' => "default",
                ];

                $extraNotificationData = [
                    "store_id" => $store_id,
                    "cart_id" => $cart_id,
                    'title' => "New Order",
                    'body' => $notification_text,
                ];

                $fcmNotification = [
                    'to'        => $token,
                    'data' => $extraNotificationData,
                    'content_available' => false, //important for iOS
                    'priority' => "high",
                    // 'time_to_live' => 5000,
                    'requireInteraction' => true,
                    'actions' => [
                        'action' => "accept",
                        'title' => "Accept"
                    ], [
                        'action' => "reject",
                        'title' => "Reject"
                    ]
                ];

                $headers = [
                    'Authorization: key=' . $getFcmKey,
                    'Content-Type: application/json'
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $fcmUrl);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
                $result = curl_exec($ch);
                curl_close($ch);

                ///////send notification to store//////

                $dd = DB::table('store_notification')
                    ->insert([
                        'store_id' => $store_id,
                        'not_title' => $notification_title,
                        'not_message' => $notification_text
                    ]);

                $results = json_decode($result);
            }

            ////rewards earned////
            $checkre = DB::table('reward_points')
                ->where('min_cart_value', '<=', $price2)
                ->orderBy('min_cart_value', 'desc')
                ->first();
            if ($checkre) {
                $reward_point = $checkre->reward_point;

                $inreward = DB::table('users')
                    ->where('user_id', $user_id)
                    ->update(['rewards' => $reward_point]);

                $cartreward = DB::table('cart_rewards')
                    ->insert(['cart_id' => $cart_id, 'rewards' => $reward_point, 'user_id' => $user_id]);
            }

            $message = array('status' => '1', 'message' => 'Order Placed successfully', 'data' => $orderr1);
            return $message;
        } else {
            $walletamt = 0;
            $prii = $price2 + $charge;
            if ($request->wallet == 'yes' || $request->wallet == 'Yes' || $request->wallet == 'YES') {
                if ($ph->wallet >= $prii) {
                    $rem_amount = 0;
                    $walletamt = $prii;
                    $rem_wallet = $ph->wallet - $prii;
                    $walupdate = DB::table('users')
                        ->where('user_id', $user_id)
                        ->update(['wallet' => $rem_wallet]);
                    $payment_status = "success";
                    $payment_method = "wallet";
                } else {

                    $rem_amount =  $prii - $ph->wallet;
                    $walletamt = $ph->wallet;
                    $rem_wallet = 0;
                    $walupdate = DB::table('users')
                        ->where('user_id', $user_id)
                        ->update(['wallet' => $rem_wallet]);
                }
            } else {
                $rem_amount =  $prii;
                $walletamt = 0;
            }
            if ($payment_status == 'success') {
                $oo = DB::table('orders')
                    ->where('cart_id', $cart_id)
                    ->update([
                        'paid_by_wallet' => $walletamt,
                        'rem_price' => $rem_amount,
                        'payment_method' => $payment_method,
                        'payment_status' => 'success'
                    ]);
                $sms = DB::table('notificationby')
                    ->select('sms')
                    ->where('user_id', $user_id)
                    ->first();
                $sms_status = (!empty($sms)) ? $sms->sms:0;
                if ($sms_status == 1) {
                    $codorderplaced = $this->ordersuccessfull($cart_id, $prod_name, $price2, $delivery_date, $time_slot, $user_phone);
                }
                /////send mail
                $email = DB::table('notificationby')
                    ->select('email', 'app')
                    ->where('user_id', $user_id)
                    ->first();
                $email_status = !empty($email)?$email->email:0;
                $q = DB::table('users')
                    ->select('user_email', 'user_name')
                    ->where('user_id', $user_id)
                    ->first();
                $user_email = $q->user_email;
                $user_name = $q->user_name;
                if ($email_status == 1) {
                    $orderplaced = $this->orderplacedMail($cart_id, $prod_name, $price2, $delivery_date, $time_slot, $user_email, $user_name);
                }
                $app_status = !empty($email)?$email->app:0;
                if ($app_status == 1) {
                    $notification_title = "WooHoo! Your Order is Placed";
                    $notification_text = "Order Successfully Placed: Your order id #" . $cart_id . " contains of " . $prod_name . " of price rs " . $price2 . " is placed Successfully.You can expect your item(s) will be delivered on " . $delivery_date;

                    $date = date('d-m-Y');


                    $getDevice = DB::table('users')
                        ->where('user_id', $user_id)
                        ->select('device_id')
                        ->first();
                    $created_at = Carbon::now();

                    if ($getDevice) {


                        $getFcm = DB::table('fcm')
                            ->where('id', '1')
                            ->first();

                        $getFcmKey = $getFcm->server_key;
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
                            'Authorization: key=' . $getFcmKey,
                            'Content-Type: application/json'
                        ];

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $fcmUrl);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
                        $result = curl_exec($ch);
                        curl_close($ch);


                        $dd = DB::table('user_notification')
                            ->insert([
                                'user_id' => $user_id,
                                'noti_title' => $notification_title,
                                'noti_message' => $notification_text
                            ]);

                        $results = json_decode($result);
                    }
                }
                $orderr1 = DB::table('orders')
                    ->where('cart_id', $cart_id)
                    ->first();

                ///////send notification to store//////

                $notification_title = "WooHoo ! You Got a New Order";
                $notification_text = "you got an order cart id #" . $cart_id . " contains of " . $prod_name . " of price rs " . $price2 . ". It will have to delivered on " . $delivery_date;

                $date = date('d-m-Y');
                $getUser = DB::table('store')
                    ->get();

                $getDevice = DB::table('store')
                    ->where('store_id', $store_id)
                    ->select('device_id')
                    ->first();
                $created_at = Carbon::now();

                if ($getDevice) {
                    
                    $getFcm = DB::table('fcm')
                    ->where('id', '1')
                    ->first();

                    $getFcmKey = $getFcm->store_server_key;
                    $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
                    $token = $getDevice->device_id;
    
                    $body = json_encode(array('message' => $notification_text, 'cart_id' => $cart_id));
                    $notification = [
                        'title' => "New Order",
                        'body' => "You Got a new order",
                        'android_channel_id' => 'AyesZ_Vendor_Notifications',
                        'sound' => "default",
                    ];
    
                    $extraNotificationData = [
                        "store_id" => $store_id,
                        "cart_id" => $cart_id,
                        'title' => "New Order",
                        'body' => $notification_text,
                    ];
    
                    $fcmNotification = [
                        'to'        => $token,
                        'data' => $extraNotificationData,
                        'content_available' => false, //important for iOS
                        'priority' => "high",
                        // 'time_to_live' => 5000,
                        'requireInteraction' => true,
                        'actions' => [
                            'action' => "accept",
                            'title' => "Accept"
                        ], [
                            'action' => "reject",
                            'title' => "Reject"
                        ]
                    ];

                    $headers = [
                        'Authorization: key=' . $getFcmKey,
                        'Content-Type: application/json'
                    ];

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $fcmUrl);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
                    $result = curl_exec($ch);
                    curl_close($ch);

                    ///////send notification to store//////

                    $dd = DB::table('store_notification')
                        ->insert([
                            'store_id' => $store_id,
                            'not_title' => $notification_title,
                            'not_message' => $notification_text
                        ]);

                    $results = json_decode($result);
                }
                ////rewards earned////
                $checkre = DB::table('reward_points')
                    ->where('min_cart_value', '<=', $price2)
                    ->orderBy('min_cart_value', 'desc')
                    ->first();
                if ($checkre) {
                    $reward_point = $checkre->reward_point;

                    $inreward = DB::table('users')
                        ->where('user_id', $user_id)
                        ->update(['rewards' => $reward_point]);

                    $cartreward = DB::table('cart_rewards')
                        ->insert(['cart_id' => $cart_id, 'rewards' => $reward_point, 'user_id' => $user_id]);
                }
                $message = array('status' => '2', 'message' => 'Order Placed successfully', 'data' => $orderr1);
                return $message;
            } else {
                $oo = DB::table('orders')
                    ->where('cart_id', $cart_id)
                    ->update([
                        'paid_by_wallet' => 0,
                        'rem_price' => $rem_amount,
                        'payment_method' => NULL,
                        'payment_status' => 'failed'
                    ]);
                $message = array('status' => '0', 'message' => 'Payment Failed');
                return $message;
            }
        }
    }

    public function delivery_info(Request $request)
    {
        $data = $request->order_array;
        $data_array = json_decode($data);
        $user_id = $request->user_id;
        $store_id = $request->store_id;

        $check_user = DB::table('users')->where('user_id', $user_id)->first();
        if (!empty($check_user)) {
            if ($check_user->is_verified == 0) {
                DB::table('users')->where('user_id', $user_id)->delete();
                return array('status' => '0', 'message' => 'Please register again & verify your account to continue.');
            }
        } else {
            return array('status' => '0', 'message' => 'Please register again & verify your account to continue.');
        }

        $open = true;
        $openTime = "is closed now";
        $store = DB::table('store')->where('store_id', $store_id)->first();
        $avails = DB::table('vendor_availability')->where('store_id', $store_id)->where('day', strtolower(date('D')))->first();
        $st_time = null;
        if (!empty($avails) && $avails->status == 1) {
            $start_time = explode(',', $avails->start_time);
            $end_time = explode(',', $avails->end_time);
            if ($start_time[0] == '24') {
                $open = true;
            } else {
                for ($i = 0; $i < sizeof($start_time); $i++) {
                    $curr_time = new DateTime("now");
                    $st_time = new DateTime(date('Y-m-d') . ' ' . $start_time[$i] . ':00');
                    $en_time = new DateTime(date('Y-m-d') . ' ' . $end_time[$i] . ':00');

                    if ($curr_time < $st_time) {
                        $open = false;
                        break;
                    }
                    if ($curr_time > $st_time && $curr_time < $en_time) {
                        $open = true;
                        break;
                    } else {
                        $open = false;
                    }
                }
            }
        } else if (!empty($avails)) {
            $open = false;
        }
        if ($store->availability == 0) {
            $open = false;
        }
        if (!$open) {
            $curr_time = new DateTime("now");
            if ($st_time != null && $curr_time < $st_time) {
                $openTime = "will open at " . $st_time->format('h:i a');
            } else {
                $avails = DB::table('vendor_availability')->where('store_id', $store_id)->get();
                $start = false;
                $will_open = false;
                for ($i = 0; $i < count($avails); $i++) {
                    if ($start) {
                        if ($avails[$i]->status == 1) {
                            $start_time = explode(',', $avails[$i]->start_time);
                            if ($start_time[0] == '24') {
                                $openTime = "will open at 12:00 AM";
                            } else if (count($start_time) > 0) {
                                $st_time = new DateTime(date('Y-m-d') . ' ' . $start_time[0] . ':00');
                                if (strtolower(date('D', strtotime(' +1 day'))) == $avails[$i]->day)
                                    $openTime = "will open tomorrow at " . $st_time->format('h:i a');
                                else
                                    $openTime = "will open on " . Helper::getFullDay($avails[$i]->day) . " at " . $st_time->format('h:i a');
                            }
                            $will_open = true;
                            break;
                        }
                    }
                    if ($avails[$i]->day == strtolower(date('D'))) {
                        $start = !$start;
                        if ($start == false) {
                            break;
                        }
                    }
                    if ($i == count($avails) - 1) {
                        $i = -1;
                    }
                }
                if (!$will_open) {
                    $openTime = 'is temporarily closed';
                }
            }
            $message = array('status' => '0', 'message' => 'Please update your App to continue.', 'open_time' => 'Store ' . $openTime);
            return $message;
        } else {
            $del_charge = $this->get_delivery_charge($user_id, $store_id, $data_array);
            if ($del_charge == "false") {
                $message = array('status' => '0', 'message' => 'Please select any address.');
            } else {
                $message = array('status' => '1', 'message' => 'Delivery Charge', 'del_charge' => $del_charge);
            }
            return $message;
        }
    }

    function get_delivery_charge($user_id, $store_id, $data_array)
    {
        $current = Carbon::now();
        $user = DB::table('address')
            ->select('society', 'city', 'lat', 'lng', 'address_id')
            ->where('user_id', $user_id)
            ->where('select_status', 1)
            ->first();
        if (!$user) {
            return "false";
        }
        $cart_price = 0.0;
        $weight = 0.0;
        $cat_id = 0;
        foreach ($data_array as $h) {
            $varient_id = $h->varient_id;
            $p =  DB::table('store_products')
                ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                ->Leftjoin('deal_product', 'product_varient.varient_id', '=', 'deal_product.varient_id')
                ->where('product_varient.varient_id', $varient_id)
                ->where('store_products.store_id', $store_id)
                ->first();
            if ($p->deal_price != NULL &&  $p->valid_from < $current && $p->valid_to > $current) {
                $price = $p->deal_price;
            } else {
                $price = $p->price;
            }


            $mrpprice = $p->mrp;
            $order_qty = $h->qty;

            $cat_id = $p->cat_id;
            $cart_price += $price * $order_qty;
            $weight += $p->weight * $order_qty;
        }
        $store = DB::table('store')
            ->select('lat', 'lng')
            ->where('store_id', $store_id)
            ->first();

        $delivery = DB::table('delivery_charge')
            ->where('cat_id', $cat_id)
            ->first();
        if (!empty($delivery)) {
            $min = explode('*', $delivery->min);
            $max = explode('*', $delivery->max);
            $charge = explode('*', $delivery->charge);
            $del_charge = 0.0;

            if ($delivery->charge_type == 'by_weight') {
                $value = $weight;
            } else if ($delivery->charge_type == 'by_distance') {
                $value = $this->get_distance($user->lat, $user->lng, $store->lat, $store->lng);
            } else {
                $value = $cart_price;
            }
            for ($i = 0; $i < sizeof($min); $i++) {
                if ($value >= $min[$i] && $value <= $max[$i]) {
                    $del_charge = $charge[$i];
                    break;
                }
            }
            return $del_charge;
        } else {
            $del_fee = DB::table('freedeliverycart')
                ->first();

            if ($del_fee) {
                return $del_fee->del_charge;
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

    public function getOrderPaymentStatus(Request $request){
        $cartId = $request->cart_id;
        $order = DB::table('orders')
            ->where('cart_id', $cartId)
            ->first();
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        // $payment = $api->payment->fetch($paymentId);
        $rp_order = $api->order->fetch($order->payment_order_id)->payments();
        return array('status'=>'1', 'data'=>$rp_order->items[0]->status,'order'=>$order);
    }


    public function ongoing(Request $request)
    {
        $user_id = $request->user_id;
        $ongoing = DB::table('orders')
            ->leftJoin('delivery_boy', 'orders.dboy_id', '=', 'delivery_boy.dboy_id')
            ->where('orders.user_id', $user_id)
            ->where('orders.order_status', '!=', 'Completed')
            ->where('orders.payment_method', '!=', NULL)
            ->orderBy('orders.order_id', 'DESC')
            ->get();

        if (count($ongoing) > 0) {
            foreach ($ongoing as $ongoings) {
                $order = DB::table('store_orders')
                    ->leftJoin('product_varient', 'store_orders.varient_id', '=', 'product_varient.varient_id')
                    ->select('store_orders.*', 'product_varient.description')
                    ->where('store_orders.order_cart_id', $ongoings->cart_id)
                    ->orderBy('store_orders.order_date', 'DESC')
                    ->get();


                $data[] = array('order_status' => $ongoings->order_status, 'delivery_date' => $ongoings->delivery_date, 'time_slot' => $ongoings->time_slot, 'payment_method' => $ongoings->payment_method, 'payment_status' => $ongoings->payment_status, 'paid_by_wallet' => $ongoings->paid_by_wallet, 'cart_id' => $ongoings->cart_id, 'price' => $ongoings->total_price, 'del_charge' => $ongoings->delivery_charge, 'remaining_amount' => $ongoings->rem_price, 'coupon_discount' => $ongoings->coupon_discount, 'dboy_name' => $ongoings->boy_name, 'dboy_phone' => $ongoings->boy_phone, 'data' => $order);
            }
        } else {
            $data = array('data' => []);
        }
        return $data;
    }

    public function cancel_for(Request $request)
    {
        $cancelfor = DB::table('cancel_for')
            ->get();

        if ($cancelfor) {
            $message = array('status' => '1', 'message' => 'Cancelling reason list', 'data' => $cancelfor);
            return $message;
        } else {
            $message = array('status' => '0', 'message' => 'no list found', 'data' => []);
            return $message;
        }
    }

    public function delete_order(Request $request)
    {
        $cart_id = $request->cart_id;
        $order = DB::table('orders')
            ->where('cart_id', $cart_id)
            ->first();
        $user_id1 = $order->user_id;
        $user = DB::table('users')
            ->where('user_id', $user_id1)
            ->first();
        $reason = $request->reason;

        if($order->order_status == 'Cancelled'){
            return array('status' => '1', 'message' => 'Order Already Cancelled', 'data' => $order);
        }

        $order_status = 'Cancelled';
        $updated_at = Carbon::now();
        $update = DB::table('orders')
            ->where('cart_id', $cart_id)
            ->update([
                'cancelling_reason' => $reason,
                'order_status' => $order_status,
                'updated_at' => $updated_at
            ]);

        if ($update) {
            //Auto Refund Code
            /* $refund = 0;
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
            

            $var = DB::table('store_orders')
                ->where('order_cart_id', $cart_id)
                ->get();
            $price2 = $order->rem_price;
            $user_data = DB::table('users')
                ->select('user_phone', 'wallet')
                ->where('user_id', $user_id1)
                ->first();
            $user_phone = $user_data->user_phone;
            foreach ($var as $h) {
                $varient_id = $h->varient_id;
                $p = DB::table('store_orders')
                    ->where('order_cart_id', $cart_id)
                    ->where('varient_id', $varient_id)
                    ->first();
                $price = $p->price;
                $order_qty = $h->qty;
                $unit[] = $p->unit;
                $qty[] = $p->quantity;
                $p_name[] = $p->product_name . "(" . $p->quantity . $p->unit . ")*" . $order_qty;
                $prod_name = implode(',', $p_name);
            }

            $store_id = $order->store_id;
            $dboy_id = $order->dboy_id;
            $store = DB::table('store')->where('store_id', $store_id)->first();
            $flag = "false";
            $orderplacedmsg = $this->ordercancel($cart_id, $prod_name, $price2, $store->phone_number);
            if ($dboy_id != 0) {
                $dboy = DB::table('delivery_boy')->where('dboy_id', $dboy_id)->first();
                $orderplacedmsg = $this->ordercancel($cart_id, $prod_name, $price2, $dboy->boy_phone);
                $flag = "true";
            }


            $message = array('status' => '1', 'message' => 'order cancelled', 'data' => $order);
            return $message;
        } else {
            $message = array('status' => '0', 'message' => 'something went wrong', 'data' => []);
            return $message;
        }
    }

    public function top_selling(Request $request)
    {
        $current = Carbon::now();
        $lat = $request->lat;
        $lng = $request->lng;
        $cityname = $request->city;
        $city = ucfirst($cityname);
        $nearbystore = DB::table('store')
            ->select('del_range', 'store_id', DB::raw("6371 * acos(cos(radians(" . $lat . ")) 
                    * cos(radians(store.lat)) 
                    * cos(radians(store.lng) - radians(" . $lng . ")) 
                    + sin(radians(" . $lat . ")) 
                    * sin(radians(store.lat))) AS distance"))
            ->where('store.del_range', '>=', 'distance')
            ->orderBy('distance')
            ->first();
        if ($nearbystore->del_range >= $nearbystore->distance) {
            $topselling = DB::table('store_products')
                ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                ->Leftjoin('store_orders', 'store_products.varient_id', '=', 'store_orders.varient_id')
                ->Leftjoin('orders', 'store_orders.order_cart_id', '=', 'orders.cart_id')
                ->Leftjoin('deal_product', 'product_varient.varient_id', '=', 'deal_product.varient_id')
                ->select('store_products.store_id', 'store_products.stock', 'product_varient.varient_id', 'product.product_id', 'product.product_name', 'product.product_image', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', DB::raw('count(store_orders.varient_id) as count'), 'product.cat_id')
                ->groupBy('store_products.store_id', 'store_products.stock', 'product_varient.varient_id', 'product.product_id', 'product.product_name', 'product.product_image', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity')
                ->where('store_products.store_id', $nearbystore->store_id)
                ->where('deal_product.deal_price', NULL)
                ->where('store_products.price', '!=', NULL)
                ->where('product.hide', 0)
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get();

            if (count($topselling) > 0) {
                $message = array('status' => '1', 'message' => 'top selling products', 'data' => $topselling);
                return $message;
            } else {
                $message = array('status' => '0', 'message' => 'nothing in top', 'data' => []);
                return $message;
            }
        } else {
            $message = array('status' => '2', 'message' => 'No Products Found Nearby', 'data' => []);
            return $message;
        }
    }


    public function whatsnew(Request $request)
    {
        $current = Carbon::now();
        $lat = $request->lat;
        $lng = $request->lng;
        $cityname = $request->city;
        $city = ucfirst($cityname);
        $nearbystore = DB::table('store')
            ->select('del_range', 'store_id', DB::raw("6371 * acos(cos(radians(" . $lat . ")) 
                    * cos(radians(store.lat)) 
                    * cos(radians(store.lng) - radians(" . $lng . ")) 
                    + sin(radians(" . $lat . ")) 
                    * sin(radians(store.lat))) AS distance"))
            ->where('store.del_range', '>=', 'distance')
            ->orderBy('distance')
            ->first();
        if ($nearbystore->del_range >= $nearbystore->distance) {
            $new = DB::table('store_products')
                ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                ->Leftjoin('deal_product', 'product_varient.varient_id', '=', 'deal_product.varient_id')
                ->select('store_products.store_id', 'store_products.stock', 'product_varient.varient_id', 'product.product_id', 'product.product_name', 'product.product_image', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', 'product.cat_id')
                ->limit(10)
                ->where('store_products.store_id', $nearbystore->store_id)
                ->where('deal_product.deal_price', NULL)
                ->where('store_products.price', '!=', NULL)
                ->where('product.hide', 0)
                ->orderByRaw('RAND()')
                ->get();

            if (count($new) > 0) {
                $message = array('status' => '1', 'message' => 'New in App', 'data' => $new);
                return $message;
            } else {
                $message = array('status' => '0', 'message' => 'nothing in new', 'data' => []);
                return $message;
            }
        } else {
            $message = array('status' => '2', 'message' => 'No Products Found Nearby', 'data' => []);
            return $message;
        }
    }


    public function recentselling(Request $request)
    {
        $current = Carbon::now();
        $lat = $request->lat;
        $lng = $request->lng;
        $cityname = $request->city;
        $city = ucfirst($cityname);
        $nearbystore = DB::table('store')
            ->select('del_range', 'store_id', DB::raw("6371 * acos(cos(radians(" . $lat . ")) 
                    * cos(radians(store.lat)) 
                    * cos(radians(store.lng) - radians(" . $lng . ")) 
                    + sin(radians(" . $lat . ")) 
                    * sin(radians(store.lat))) AS distance"))
            ->where('store.del_range', '>=', 'distance')
            ->orderBy('distance')
            ->first();
        if ($nearbystore->del_range >= $nearbystore->distance) {
            $recentselling = DB::table('store_products')
                ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                ->Leftjoin('store_orders', 'product_varient.varient_id', '=', 'store_orders.varient_id')
                ->Leftjoin('orders', 'store_orders.order_cart_id', '=', 'orders.cart_id')
                ->Leftjoin('deal_product', 'product_varient.varient_id', '=', 'deal_product.varient_id')
                ->select('store_products.store_id', 'store_products.stock', 'product_varient.varient_id', 'product.product_id', 'product.product_name', 'product.product_image', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity', DB::raw('count(store_orders.varient_id) as count'), 'product.cat_id')
                ->groupBy('store_products.store_id', 'store_products.stock', 'product_varient.varient_id', 'product.product_id', 'product.product_name', 'product.product_image', 'product_varient.description', 'store_products.price', 'store_products.mrp', 'product_varient.varient_image', 'product_varient.unit', 'product_varient.quantity')
                ->where('store_products.store_id', $nearbystore->store_id)
                ->orderByRaw('RAND()')
                ->where('deal_product.deal_price', NULL)
                ->where('product.hide', 0)
                ->where('store_products.price', '!=', NULL)
                ->limit(10)
                ->get();

            if (count($recentselling) > 0) {
                $message = array('status' => '1', 'message' => 'recent selling products', 'data' => $recentselling);
                return $message;
            } else {
                $message = array('status' => '0', 'message' => 'nothing in top', 'data' => []);
                return $message;
            }
        } else {
            $message = array('status' => '2', 'message' => 'No Products Found Nearby', 'data' => []);
            return $message;
        }
    }

    public function completed_orders(Request $request)
    {
        $user_id = $request->user_id;
        $completeds = DB::table('orders')
            ->leftJoin('delivery_boy', 'orders.dboy_id', '=', 'delivery_boy.dboy_id')
            ->where('orders.user_id', $user_id)
            ->where('orders.order_status', 'Completed')
            ->get();

        if (count($completeds) > 0) {
            foreach ($completeds as $completed) {
                $order = DB::table('store_orders')
                    ->leftJoin('product_varient', 'store_orders.varient_id', '=', 'product_varient.varient_id')
                    ->select('store_orders.*', 'product_varient.description')
                    ->where('store_orders.order_cart_id', $completed->cart_id)
                    ->orderBy('store_orders.order_date', 'DESC')
                    ->get();

                $rating = OrderRating::where('order_id',$completed->order_id)->count();
                // $deliveryRating = DeliveryRating::where('order_id',$completeds)->get();


                $data[] = array(
                    'order_status' => $completed->order_status, 
                    'delivery_date' => $completed->delivery_date, 
                    'time_slot' => $completed->time_slot, 
                    'payment_method' => $completed->payment_method, 
                    'payment_status' => $completed->payment_status, 
                    'paid_by_wallet' => $completed->paid_by_wallet, 
                    'cart_id' => $completed->cart_id, 
                    'price' => $completed->total_price, 
                    'del_charge' => $completed->delivery_charge, 
                    'remaining_amount' => $completed->rem_price, 
                    'coupon_discount' => $completed->coupon_discount, 
                    'dboy_name' => $completed->boy_name, 
                    'dboy_phone' => $completed->boy_phone, 
                    'data' => $order,
                    'rating'=>$rating);
            }
        } else {
            $data = array('data' => []);
        }
        return $data;
    }




    public function can_orders(Request $request)
    {
        $user_id = $request->user_id;
        $completed = DB::table('orders')
            ->where('user_id', $user_id)
            ->where('order_status', 'cancelled')
            ->get();

        if (count($completed) > 0) {
            foreach ($completed as $completeds) {
                $order = DB::table('store_orders')
                    ->join('product_varient', 'store_orders.varient_id', '=', 'product_varient.varient_id')
                    ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                    ->select('product_varient.varient_id', 'product.product_name', 'product_varient.varient_image', 'store_orders.qty', 'product_varient.description', 'product_varient.unit', 'product_varient.quantity', 'store_orders.order_cart_id')
                    ->where('store_orders.order_cart_id', $completeds->cart_id)
                    ->groupBy('product_varient.varient_id', 'product.product_name', 'product_varient.varient_image', 'store_orders.qty', 'product_varient.description', 'product_varient.unit', 'product_varient.quantity', 'store_orders.order_cart_id')
                    ->orderBy('store_orders.order_date', 'DESC')
                    ->get();


                $data[] = array('cart_id' => $completeds->cart_id, 'price' => $completeds->total_price, 'del_charge' => $completeds->delivery_charge, 'data' => $order);
            }
        } else {
            $data[] = array('data' => 'No Orders Cancelled Yet');
        }
        return $data;
    }
}
