<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use Carbon\Carbon;
use App\Traits\SendSms;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AssignorderController extends Controller
{
    use SendSms;

    public function allOrders(Request $request)
    {
        $email = Session::get('bamaStore');
        $store = DB::table('store')
            ->where('email', $email)
            ->first();
        if (empty($store)) {
            return redirect('store/logout');
        }
        $title = "All Orders";
        $logo = DB::table('tbl_web_setting')
            ->where('set_id', '1')
            ->first();
        $currency = DB::table('currency')->first()->currency_sign;

        return view('store.orders.list', compact('title', 'logo', 'store', 'currency', 'request'));
    }

    public function getOrders(Request $request)
    {
        $email = Session::get('bamaStore');
        $store = DB::table('store')
            ->where('email', $email)
            ->first();
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $searchValue = $search_arr['value']; // Search value

        // Total records
        $totalRecords = DB::table('orders')->select('count(*) as allcount')->count();
        $totalRecordswithFilter = DB::table('orders')->select('count(*) as allcount')
            ->where('cart_id', 'like', '%' . $searchValue . '%')
            ->count();

        // Fetch records
        $records = DB::table('orders')
            ->where('orders.cart_id', 'like', '%' . $searchValue . '%')
            ->leftjoin('users', 'users.user_id', 'orders.user_id')
            ->leftJoin('store', 'store.store_id', 'orders.store_id')
            ->leftJoin('delivery_boy', 'delivery_boy.dboy_id', 'orders.dboy_id')
            ->where('payment_method', '!=', NULL)
            ->where('orders.store_id', $store->store_id)
            ->orderBy('created_at','DESC')
            ->select(
                'orders.*',
                'users.user_name',
                'users.user_phone',
                'store.store_name',
                'store.phone_number as store_phone',
                DB::raw("IFNULL(delivery_boy.boy_name,'') as boy_name"),
                DB::raw("IFNULL(delivery_boy.boy_phone,'') as boy_phone")
            )
            ->skip($start)
            ->take($rowperpage);
        if(!empty($columnIndex_arr)){
            $columnIndex = $columnIndex_arr[0]['column']; // Column index
            $columnName = $columnName_arr[$columnIndex]['data']; // Column name
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
            $records = $records->orderBy($columnName, $columnSortOrder);
        }
        

        if (!empty($request->order_status)) {
            $orderStatus = explode(',', $request->order_status);
            $records = $records->whereIn('orders.order_status', $orderStatus);
        }

        $records = $records->get();

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $records
        );

        echo json_encode($response);
        exit;
    }


    public function showOrder($id)
    {
        $email = Session::get('bamaStore');
        $store = DB::table('store')
            ->where('email', $email)
            ->first();
        $order = DB::table('orders')->where('cart_id', $id)->where('store_id', $store->store_id)->first();
        if ($order) {
            $title = "Order Details";
            $logo = DB::table('tbl_web_setting')
                ->where('set_id', '1')
                ->first();

            $items = DB::table('store_orders')->where('order_cart_id', $order->cart_id)->get();
            $store = DB::table('store')->where('store_id', $order->store_id)->first();
            $user = DB::table('users')->where('user_id', $order->user_id)->first();
            $address = DB::table('address')->where('address_id', $order->address_id)->first();

            $dboy = DB::table('delivery_boy')->where('dboy_id', $order->dboy_id)
                ->select('boy_name', 'boy_phone')->first();
            $currency = DB::table('currency')->first()->currency_sign;

            return view('store.orders.show', compact('title', 'logo', 'store', 'order', 'items', 'store', 'user', 'dboy', 'address', 'currency'));
        } else {
            return redirect()->back()->withErrors("Order Not Found");
        }
    }

    public function assignedorders(Request $request)
    {
        $title = "Order section (Today)";
        $email = Session::get('bamaStore');
        $store = DB::table('store')
            ->where('email', $email)
            ->first();
        if (empty($store)) {
            return redirect('store/logout');
        }
        $logo = DB::table('tbl_web_setting')
            ->where('set_id', '1')
            ->first();
        $date = date('Y-m-d');
        $ord = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.user_id')
            ->leftjoin('delivery_boy', 'orders.dboy_id', '=', 'delivery_boy.dboy_id')
            ->where('orders.store_id', $store->store_id)
            ->where('orders.delivery_date', $date)
            ->where('payment_method', '!=', NULL)
            ->where('orders.order_status', '!=', 'cancelled')
            ->where('orders.order_status', '!=', 'Completed')
            ->orderBy('created_at','DESC')
            ->get();

        $details  =   DB::table('orders')
            ->join('store_orders', 'orders.cart_id', '=', 'store_orders.order_cart_id')
            ->where('orders.store_id', $store->store_id)
            ->where('store_orders.store_approval', 1)
            ->get();

        $nearbydboy = DB::table('delivery_boy')
            ->leftJoin('orders', 'delivery_boy.dboy_id', '=', 'orders.dboy_id')
            ->select("delivery_boy.boy_name", "delivery_boy.dboy_id", "delivery_boy.lat", "delivery_boy.lng", "delivery_boy.boy_city", DB::raw("Count(orders.order_id)as count"), DB::raw("6371 * acos(cos(radians(" . $store->lat . ")) 
                * cos(radians(delivery_boy.lat)) 
                * cos(radians(delivery_boy.lng) - radians(" . $store->lng . ")) 
                + sin(radians(" . $store->lat . ")) 
                * sin(radians(delivery_boy.lat))) AS distance"))
            ->groupBy("delivery_boy.boy_name", "delivery_boy.dboy_id", "delivery_boy.lat", "delivery_boy.lng", "delivery_boy.boy_city")
            ->where('delivery_boy.boy_city', $store->city)
            ->orderBy('distance')
            ->get();
        return view('store.orders.assignedorders', compact('title', 'logo', 'ord', 'store', 'details', 'nearbydboy'));
    }


    public function orders(Request $request)
    {
        $title = "Order section (Next Day)";
        $date = date('Y-m-d');
        $day = 1;
        $next_date = date('Y-m-d', strtotime($date . ' + ' . $day . ' days'));
        $email = Session::get('bamaStore');
        $store = DB::table('store')
            ->where('email', $email)
            ->first();
        if (empty($store)) {
            return redirect('store/logout');
        }
        $logo = DB::table('tbl_web_setting')
            ->where('set_id', '1')
            ->first();

        $ord = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.user_id')
            ->leftjoin('delivery_boy', 'orders.dboy_id', '=', 'delivery_boy.dboy_id')
            ->where('orders.store_id', $store->store_id)
            ->where('orders.delivery_date', $next_date)
            ->where('orders.order_status', '!=', 'cancelled')
            ->where('orders.order_status', '!=', 'Completed')
            ->where('payment_method', '!=', NULL)
            ->orderBy('created_at','DESC')
            ->get();

        $details  =   DB::table('orders')
            ->join('store_orders', 'orders.cart_id', '=', 'store_orders.order_cart_id')
            ->where('orders.store_id', $store->store_id)
            ->where('store_orders.store_approval', 1)
            ->get();
        $store_id = $store->store_id;

        $nearbydboy = DB::table('delivery_boy')
            ->leftJoin('orders', 'delivery_boy.dboy_id', '=', 'orders.dboy_id')
            ->select("delivery_boy.boy_name", "delivery_boy.dboy_id", "delivery_boy.lat", "delivery_boy.lng", "delivery_boy.boy_city", DB::raw("Count(orders.order_id)as count"), DB::raw("6371 * acos(cos(radians(" . $store->lat . ")) 
                * cos(radians(delivery_boy.lat)) 
                * cos(radians(delivery_boy.lng) - radians(" . $store->lng . ")) 
                + sin(radians(" . $store->lat . ")) 
                * sin(radians(delivery_boy.lat))) AS distance"))
            ->groupBy("delivery_boy.boy_name", "delivery_boy.dboy_id", "delivery_boy.lat", "delivery_boy.lng", "delivery_boy.boy_city")
            ->where('delivery_boy.boy_city', $store->city)
            ->orderBy('distance')
            ->get();
        return view('store.orders.orders', compact('title', 'logo', 'ord', 'store', 'details', 'nearbydboy'));
    }

    public function confirm_order(Request $request)
    {
        $cart_id = $request->cart_id;
        $dboy_id = $request->dboy_id;
        $email = Session::get('bamaStore');
        $store = DB::table('store')
            ->where('email', $email)
            ->first();
        if (empty($store)) {
            return redirect('store/logout');
        }
        $store_id = $store->store_id;
        $curr = DB::table('currency')
            ->first();

        $orr =   DB::table('orders')
            ->where('cart_id', $cart_id)
            ->first();

        if($orr->dboy_id!=0){
            return redirect()->back()->withErrors('Order already confirmed');
        }
        $userssss =  DB::table('users')
            ->where('user_id', $orr->user_id)
            ->first();
        $user_phone = $userssss->user_phone;
        $v = DB::table('store_orders')
            ->where('order_cart_id', $cart_id)
            ->get();

        $getDDevice = DB::table('delivery_boy')
            ->where('dboy_id', $dboy_id)
            ->select('device_id', 'boy_name')
            ->first();

        foreach ($v as $vs) {
            $qt = $vs->qty;
            $pr = DB::table('store_products')
                ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                ->where('store_products.varient_id', $vs->varient_id)
                ->where('store_products.store_id', $store_id)
                ->first();

            $stoc = DB::table('store_products')
                ->where('varient_id', $vs->varient_id)
                ->where('store_id', $store_id)
                ->first();
            if ($stoc) {
                $newstock = $stoc->stock - $qt;
                $st = DB::table('store_products')
                    ->where('varient_id', $vs->varient_id)
                    ->where('store_id', $store_id)
                    ->update(['stock' => $newstock]);
            }
        }
        $nearbydboy = DB::table('delivery_boy')
            ->leftJoin('orders', 'delivery_boy.dboy_id', '=', 'orders.dboy_id')
            ->select("delivery_boy.boy_name", "delivery_boy.dboy_id", "delivery_boy.lat", "delivery_boy.lng", "delivery_boy.boy_city", DB::raw("Count(orders.order_id)as count"), DB::raw("6371 * acos(cos(radians(" . $store->lat . ")) 
                    * cos(radians(delivery_boy.lat)) 
                    * cos(radians(delivery_boy.lng) - radians(" . $store->lng . ")) 
                    + sin(radians(" . $store->lat . ")) 
                    * sin(radians(delivery_boy.lat))) AS distance"))
            ->groupBy("delivery_boy.boy_name", "delivery_boy.dboy_id", "delivery_boy.lat", "delivery_boy.lng", "delivery_boy.boy_city")
            ->where('delivery_boy.boy_city', $store->city)
            ->where('delivery_boy.status', 1)
            ->orderBy('distance')
            ->get();

        if (count($nearbydboy) > 0) {
            if (count($nearbydboy) == 1) {
                $res = $this->assignOrder($store_id, $cart_id, $nearbydboy[0]->dboy_id);
            } else {
                $assigned = false;
                $dboy_id = $nearbydboy[0]->dboy_id;
                foreach ($nearbydboy as $dboy) {
                    $orders = DB::table('orders')
                        ->where('dboy_id', $dboy->dboy_id)
                        ->where('order_status', '!=', 'Completed')
                        ->where('order_status', '!=', 'Cancelled')
                        ->where('order_status', '!=', 'Rejected_By_Delivery_Agent')
                        ->where('order_status', '!=', 'Rejected_By_Vendor')
                        ->where('order_date', date('Y-m-d'))
                        ->get();
                    if (count($orders) == 0) {
                        $dboy_id = $dboy->dboy_id;
                        break;
                    }
                }
                $res = $this->assignOrder($store_id, $cart_id, $dboy_id);
            }
            if ($res['status'] == '1') {
                $v = DB::table('store_orders')
                    ->where('order_cart_id', $cart_id)
                    ->get();

                //send sms and app notification to user//
                $sms = DB::table('notificationby')
                    ->select('sms', 'app')
                    ->where('user_id', $orr->user_id)
                    ->first();
                $sms_status = (!empty($sms))?$sms->sms:0;
                if ($sms_status == 1) {
                    $codorderplaced = $this->orderconfirmedsms($cart_id, $user_phone, $orr);
                }
                $app_status = (!empty($sms))?$sms->app:0;
                if ($app_status == 1) {
                    $notification_title = "WooHoo! Your Order is Confirmed";
                    $notification_text = "Your Order is confirmed: Your order id #" . $cart_id . " is confirmed by the store.You can expect your item(s) will be delivered on " . $orr->delivery_date . " (" . $orr->time_slot . ").";

                    $date = date('d-m-Y');


                    $getDevice = DB::table('users')
                        ->where('user_id', $orr->user_id)
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
                                'user_id' => $orr->user_id,
                                'noti_title' => $notification_title,
                                'noti_message' => $notification_text
                            ]);

                        $results = json_decode($result);
                    }
                }
                return redirect()->back()->withSuccess($res['message']);
            } else {
                return redirect()->back()->withErrors($res['message']);
            }
        } else {
            return redirect()->back()->withErrors('No Delivery Boy In Your City');
        }
    }

    public function assignOrder($store_id, $cart_id, $dboyid)
    {
        $curr = DB::table('currency')
            ->first();

        $store = DB::table('store')
            ->where('store_id', $store_id)
            ->first();

        $getDevice = DB::table('delivery_boy')
            ->where('dboy_id', $dboyid)
            ->select('device_id', 'boy_name')
            ->first();
        $orr =   DB::table('orders')
            ->where('cart_id', $cart_id)
            ->first();

        $v = DB::table('store_orders')
            ->where('order_cart_id', $cart_id)
            ->get();
        foreach ($v as $vs) {
            $qt = $vs->qty;
            $pr = DB::table('store_products')
                ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                ->where('store_products.varient_id', $vs->varient_id)
                ->where('store_products.store_id', $store_id)
                ->first();
            $stoc = DB::table('store_products')
                ->where('varient_id', $vs->varient_id)
                ->where('store_id', $store_id)
                ->first();
            if ($stoc) {
                $newstock = $stoc->stock - $qt;
                $st = DB::table('store_products')
                    ->where('varient_id', $vs->varient_id)
                    ->where('store_id', $store_id)
                    ->update(['stock' => $newstock]);
            }
        }
        $orderconfirm = DB::table('orders')
            ->where('cart_id', $cart_id)
            ->update([
                'order_status' => 'Confirmed',
                'dboy_id' => $dboyid
            ]);


        if ($orderconfirm) {
            $notification_title = "You Got a New Order for Delivery on " . $orr->delivery_date;
            $notification_text = "you got an order with cart id #" . $cart_id . " of price " . $curr->currency_sign . " " . ($orr->total_price - $orr->coupon_discount) . ". It will have to delivered on " . $orr->delivery_date;

            $date = date('d-m-Y');

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
                "dboy_id" => $dboyid,
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


            $results = json_decode($result);


            $message = array('status' => '1', 'message' => 'Order is confirmed and Assigned to ' . $getDevice->boy_name, 'orders' => count($v), 'qty' => $v[0]->qty);
            return $message;
        } else {
            $message = array('status' => '0', 'message' => 'Already Assigned to ' . $getDevice->boy_name);
            return $message;
        }
    }

    public function reject_order(Request $request)
    {
        $cart_id = $request->cart_id;
        $order = DB::table('orders')
            ->where('cart_id', $cart_id)
            ->first();
        if($order->order_status != 'Pending'){
            return redirect()->back()->withErrors("Order Already in process or completed");
        }
        $email = Session::get('bamaStore');
        $store = DB::table('store')
            ->where('email', $email)
            ->first();
        $store_id = $store->store_id;
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
        
        return redirect()->back()->withSuccess('Order Rejected successfully');
    }

    // public function reject_order(Request $request)
    // {
    //     $cart_id = $request->cart_id;
    //     $ordr = DB::table('orders')
    //         ->where('cart_id', $cart_id)
    //         ->first();
    //     $user_id1 = $ordr->user_id;
    //     $orders = DB::table('store_orders')
    //         ->where('order_cart_id', $cart_id)
    //         ->where('store_approval', 1)
    //         ->get();
    //     $curr = DB::table('currency')
    //         ->first();
    //     $email = Session::get('bamaStore');
    //     $store = DB::table('store')
    //         ->where('email', $email)
    //         ->first();

    //     $v_price1 = 0;
    //     $cartss = DB::table('store_orders')
    //         ->where('order_cart_id', $cart_id)
    //         ->where('store_approval', 0)
    //         ->get();

    //     if (count($cartss) > 0) {
    //         foreach ($cartss as $carts) {
    //             $v1 = DB::table('store_orders')
    //                 ->where('store_order_id', $carts->store_order_id)
    //                 ->first();

    //             $v_price1 += $v1->price * $v1->qty;
    //         }
    //         $user_id1 = $ordr->user_id;
    //         $userwa1 = DB::table('users')
    //             ->where('user_id', $user_id1)
    //             ->first();
    //         if ($ordr->payment_method == 'COD' || $ordr->payment_method == 'Cod' || $ordr->payment_method == 'cod') {
    //             $newbal1 = $userwa1->wallet;
    //         } else {
    //             $newbal1 = $userwa1->wallet - $v_price1;
    //         }
    //         $userwalletupdate = DB::table('users')
    //             ->where('user_id', $user_id1)
    //             ->update(['wallet' => $newbal1]);
    //     }

    //     $var = DB::table('store_orders')
    //         ->where('order_cart_id', $cart_id)
    //         ->where('store_approval', 1)
    //         ->get();
    //     $price2 = 0;
    //     foreach ($var as $h) {
    //         $varient_id = $h->varient_id;
    //         $p = DB::table('store_products')
    //             ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
    //             ->join('product', 'product_varient.product_id', '=', 'product.product_id')
    //             ->where('product_varient.varient_id', $varient_id)
    //             ->where('store_products.store_id', $store->store_id)
    //             ->first();
    //         $price = $p->price;
    //         $order_qty = $h->qty;
    //         $price2 += $price * $order_qty;
    //         $unit[] = $p->unit;
    //         $qty[] = $p->quantity;
    //         $p_name[] = $p->product_name . "(" . $p->quantity . $p->unit . ")*" . $order_qty;
    //         $prod_name = implode(',', $p_name);
    //     }

    //     if ($ordr->cancel_by_store == 0) {
    //         $cancel = 1;
    //         $store_id = DB::table('store')
    //             ->select(
    //                 "store_id",
    //                 "store_name",
    //                 DB::raw("6371 * acos(cos(radians(" . $store->lat . ")) 
    //         * cos(radians(lat)) 
    //         * cos(radians(lng) - radians(" . $store->lng . ")) 
    //         + sin(radians(" . $store->lat . ")) 
    //         * sin(radians(lat))) AS distance")
    //             )
    //             ->where('city', $store->city)
    //             ->where('store_id', '!=', $store->store_id)
    //             ->orderBy('distance')
    //             ->first();

    //         if ($store_id) {
    //             $ordupdate = DB::table('orders')
    //                 ->where('cart_id', $cart_id)
    //                 ->update([
    //                     'store_id' => $store_id->store_id,
    //                     'cancel_by_store' => $cancel,
    //                     'order_status' => 'Pending'
    //                 ]);


    //             $cart_update = DB::table('store_orders')
    //                 ->where('order_cart_id', $cart_id)
    //                 ->update(['store_approval' => 1]);


    //             ///////send notification to store//////

    //             $notification_title = "WooHoo ! You Got a New Order";
    //             $notification_text = "you got an order cart id #" . $cart_id . " contains of " . $prod_name . " of price " . $curr->currency_sign . " " . $price2 . ". It will have to delivered on " . $ordr->delivery_date . " between " . $ordr->time_slot . ".";

    //             $date = date('d-m-Y');
    //             $getUser = DB::table('store')
    //                 ->get();

    //             $getDevice = DB::table('store')
    //                 ->where('store_id', $store_id->store_id)
    //                 ->select('device_id')
    //                 ->first();
    //             $created_at = Carbon::now();


    //             $getFcm = DB::table('fcm')
    //                 ->where('id', '1')
    //                 ->first();

    //             $getFcmKey = $getFcm->store_server_key;
    //             $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
    //             $token = $getDevice->device_id;


    //             $notification = [
    //                 'title' => $notification_title,
    //                 'body' => $notification_text,
    //                 'sound' => true,
    //             ];

    //             $extraNotificationData = ["message" => $notification];

    //             $fcmNotification = [
    //                 'to'        => $token,
    //                 'notification' => $notification,
    //                 'data' => $extraNotificationData,
    //             ];

    //             $headers = [
    //                 'Authorization: key=' . $getFcmKey,
    //                 'Content-Type: application/json'
    //             ];

    //             $ch = curl_init();
    //             curl_setopt($ch, CURLOPT_URL, $fcmUrl);
    //             curl_setopt($ch, CURLOPT_POST, true);
    //             curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //             curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //             curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
    //             $result = curl_exec($ch);
    //             curl_close($ch);

    //             ///////send notification to store//////

    //             $dd = DB::table('store_notification')
    //                 ->insert([
    //                     'store_id' => $store_id->store_id,
    //                     'not_title' => $notification_title,
    //                     'not_message' => $notification_text
    //                 ]);

    //             $results = json_decode($result);
    //             return redirect()->back()->withSuccess('Order Rejected successfully');
    //         } else {
    //             $ordupdate = DB::table('orders')
    //                 ->where('cart_id', $cart_id)
    //                 ->update([
    //                     'store_id' => 0,
    //                     'cancel_by_store' => $cancel,
    //                     'order_status' => 'Pending'
    //                 ]);


    //             $cart_update = DB::table('store_orders')
    //                 ->where('order_cart_id', $cart_id)
    //                 ->update(['store_approval' => 1]);

    //             return redirect()->back()->withSuccess('Order Rejected successfully');
    //         }
    //     } else {
    //         $cancel = 2;
    //         $ordupdate = DB::table('orders')
    //             ->where('cart_id', $cart_id)
    //             ->update([
    //                 'store_id' => 0,
    //                 'cancel_by_store' => $cancel,
    //                 'order_status' => 'Pending'
    //             ]);


    //         $cart_update = DB::table('store_orders')
    //             ->where('order_cart_id', $cart_id)
    //             ->update(['store_approval' => 1]);

    //         return redirect()->back()->withSuccess('Order Rejected successfully');
    //     }
    //     return redirect()->back()->withSuccess('Order Rejected successfully');
    // }
}
