<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait AssignOrder{
    public function assign($cart_id, $dboy)
    {
        $curr = DB::table('currency')
            ->first();

        $order =   DB::table('orders')
            ->where('cart_id', $cart_id)
            ->first();

        $items = DB::table('store_orders')
            ->where('order_cart_id', $cart_id)
            ->get();
        
        $updateOrderStatus = DB::table('orders')
            ->where('cart_id', $cart_id)
            ->update([
                'order_status' => 'Confirmed',
                'dboy_id' => $dboy->dboy_id
            ]);


        if ($updateOrderStatus) {
            $notification_text = "You got an order with cart id #" . $cart_id . " of price " . $curr->currency_sign . " " . ($order->total_price - $order->coupon_discount) . ". It will have to delivered on " . $order->delivery_date;

            $getFcm = DB::table('fcm')
                ->where('id', '1')
                ->first();

            $getFcmKey = $getFcm->driver_server_key;
            $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
            $token = $dboy->device_id;


            $notificationData = [
                "dboy_id" => $dboy->dboy_id,
                "cart_id" => $cart_id,
                'title' => "New Delivery Order",
                'body' => $notification_text,
            ];

            $fcmNotification = [
                'to'        => $token,
                'data' => $notificationData,
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
            curl_exec($ch);
            curl_close($ch);


            $message = array('status' => '1', 'message' => 'Order is confirmed and Assigned to ' . $dboy->boy_name, 'orders' => count($items), 'qty' => $items[0]->qty);            return $message;
            return $message;
        } else {
            $message = array('status' => '0', 'message' => 'Already Assigned to ' . $dboy->boy_name);
            return $message;
        }
    }
}