<?php

namespace App\Http\Controllers\Admin;

use App\AdminLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Order;
use App\Traits\AssignOrder;
use Carbon\Carbon;
use App\Traits\SendMail;
use App\Traits\SendSms;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AdminorderController extends Controller
{
    use SendMail;
    use SendSms;
    use AssignOrder;

    public function allOrders(Request $request)
    {
        $title = "Orders";
        $admin_email = Session::get('bamaAdmin');
        $admin = DB::table('admin')
            ->where('admin_email', $admin_email)
            ->first();
        $logo = DB::table('tbl_web_setting')
            ->where('set_id', '1')
            ->first();
        $currency = DB::table('currency')->first()->currency_sign;

        return view('admin.all_orders.list', compact('title', 'logo', 'admin', 'currency', 'request'));
    }

    public function getOrders(Request $request)
    {
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

    public function editOrder($id)
    {
        $order = DB::table('orders')->where('order_id', $id)->first();
        if ($order) {
            $title = "Edit Order";
            $admin_email = Session::get('bamaAdmin');
            $admin = DB::table('admin')
                ->where('admin_email', $admin_email)
                ->first();
            $logo = DB::table('tbl_web_setting')
                ->where('set_id', '1')
                ->first();

            $items = DB::table('store_orders')->where('order_cart_id', $order->cart_id)->get();
            $store = DB::table('store')->where('store_id', $order->store_id)->first();
            $user = DB::table('users')->where('user_id', $order->user_id)->first();
            $address = DB::table('address')->where('address_id', $order->address_id)->first();

            $dboys = DB::table('delivery_boy')->where('status', 1)->get();
            $currency = DB::table('currency')->first()->currency_sign;
            $logs = AdminLog::where('type','order')
                    ->where('content_id',$order->order_id)
                    ->join('admin','admin.id','admin_logs.admin_id')
                    ->select('admin_logs.*','admin.admin_name','admin.admin_email')
                    ->orderBy('created_at','DESC')->get();

            return view('admin.all_orders.edit', compact('title', 'logo', 'admin', 'order', 'items', 'store', 'user', 'dboys', 'address', 'currency','logs'));
        } else {
            return redirect()->back()->withErrors("Order Not Found");
        }
    }

    public function updateOrder(Request $request, $id)
    {
        $this->validate(
            $request,
            [
                'delivery_charge' => 'required',
                'order_status' => 'required'
            ],
            [
                'delivery_charge' => 'Enter Delivery Charge',
                'order_status' => 'Select Order Status'
            ]
        );

        $admin_email = Session::get('bamaAdmin');
        $admin = DB::table('admin')
            ->where('admin_email', $admin_email)
            ->first();

        $order = Order::where('order_id', $id)->first();
        $success = array();
        $errors = array();
        if ($order) {
            // return redirect()->back()->withErrors("Order Found");
            if ($order->order_status != $request->order_status && !empty($request->order_status)) {
                $log = new AdminLog();
                $log->admin_id = $admin->id;
                $log->type = 'order';
                $log->content_id = $order->order_id;
                $log->log = 'Order status changed from "' . $order->order_status . '" to "' . $request->order_status;
                $log->save();

                $order->order_status = $request->order_status;
                $order->save();
                $success[] = $log->log;
            }
            if ($order->delivery_charge != $request->delivery_charge && !empty($request->delivery_charge)) {
                $log = new AdminLog();
                $log->admin_id = $admin->id;
                $log->type = 'order';
                $log->content_id = $order->order_id;
                $log->log = 'Delivery Charge changed from "' . $order->delivery_charge . '" to "' . $request->delivery_charge;
                $log->save();

                $order->delivery_charge = $request->delivery_charge;
                $order->save();
                $success[] = $log->log;
            }
            if ($order->dboy_id != $request->dboy_id && !empty($request->dboy_id)) {
                $dboy = DB::table('delivery_boy')->where('dboy_id', $order->dboy_id)->first();
                $new_dboy = DB::table('delivery_boy')->where('dboy_id', $request->dboy_id)->first();
                if (!empty($new_dboy)) {
                    if ($new_dboy->status != 0) {
                        $res = $this->assign($order->store_id, $order->cart_id, $order->dboy_id);
                        if($res['status']=='1'){
                            $success[] = $res["message"];
                            $log = new AdminLog();
                            $log->admin_id = $admin->id;
                            $log->type = 'order';
                            $log->content_id = $order->order_id;
                            if(!empty($dboy))
                                $log->log = 'Delivery Agent changed from "#' . $dboy->dboy_id . '-' . $dboy->boy_name . ' (' . $dboy->boy_phone . ')" to "#' . $new_dboy->dboy_id . '-' . $new_dboy->boy_name . ' (' . $new_dboy->boy_phone . ')"';
                            else
                                $log->log = 'Order assigned to delivery agent "#' . $new_dboy->dboy_id . '-' . $new_dboy->boy_name . ' (' . $new_dboy->boy_phone . ')"';
                            $log->save();

                            $order->dboy_id = $request->dboy_id;
                            $order->order_status = 'Confirmed';
                            $order->save();
                        }else{
                            $errors[] = $res["message"];
                        }
                        
                    } else {
                        $errors[] = "Selected Delivery Agent is not on duty";
                    }
                }
            }
            $items = DB::table('store_orders')->where('order_cart_id', $order->cart_id)->get();
            $item_total_price = $order->price_without_delivery;
            foreach($items as $item){
                if(!empty($request->get('item_price-'.$item->store_order_id))){
                    $new_price = $request->get('item_price-'.$item->store_order_id);
                    if($item->price != $request->get('item_price-'.$item->store_order_id)){
                        $log = new AdminLog();
                        $log->admin_id = $admin->id;
                        $log->type = 'order';
                        $log->content_id = $order->order_id;
                        $log->log = 'Order Item "'.$item->qty.' X '.$item->product_name.'"'."'s".' price changed from "' . $item->price . '" to "' . $new_price;
                        $log->save();

                        $save = DB::table('store_orders')
                                    ->where('store_order_id', $item->store_order_id)
                                    ->update(['price'=>$new_price]);
                        if($save){
                            $item_total_price = $item_total_price - $item->price + $new_price;
                        }
                        $success[] = $log->log;
                    }
                }
            }
            $order->price_without_delivery = $item_total_price;
            $order->total_price = $item_total_price + $order->delivery_charge;
            $order->rem_price = $item_total_price + $order->delivery_charge - $order->discount - $order->paid_by_wallet;
            $order->save();
            return redirect()->back()->withSuccess($success)->withErrors($errors);
        } else {
            return redirect()->back()->withErrors("Order Not Found");
        }
    }

    public function admin_com_orders(Request $request)
    {
        $title = "Completed Order section";
        $admin_email = Session::get('bamaAdmin');
        $admin = DB::table('admin')
            ->where('admin_email', $admin_email)
            ->first();
        $logo = DB::table('tbl_web_setting')
            ->where('set_id', '1')
            ->first();

        $ord = DB::table('orders')
            ->join('store', 'orders.store_id', '=', 'store.store_id')
            ->join('delivery_boy', 'orders.dboy_id', '=', 'delivery_boy.dboy_id')
            ->join('users', 'orders.user_id', '=', 'users.user_id')
            ->orderBy('orders.delivery_date', 'DESC')
            ->where('order_status', 'completed')
            ->orWhere('order_status', 'Completed')
            ->get();

        $details  =   DB::table('orders')
            ->join('store_orders', 'orders.cart_id', '=', 'store_orders.order_cart_id')
            ->where('store_orders.store_approval', 1)
            ->get();

        return view('admin.all_orders.com_orders', compact('title', 'logo', 'ord', 'details', 'admin'));
    }

    public function admin_can_orders(Request $request)
    {
        $title = "Cancelled Order section";
        $admin_email = Session::get('bamaAdmin');
        $admin = DB::table('admin')
            ->where('admin_email', $admin_email)
            ->first();
        $logo = DB::table('tbl_web_setting')
            ->where('set_id', '1')
            ->first();

        $ord = DB::table('orders')
            ->leftjoin('store', 'orders.store_id', '=', 'store.store_id')
            ->leftjoin('delivery_boy', 'orders.dboy_id', '=', 'delivery_boy.dboy_id')
            ->join('users', 'orders.user_id', '=', 'users.user_id')
            ->orderBy('orders.delivery_date', 'DESC')
            ->where('order_status', 'cancelled')
            ->orWhere('order_status', 'Cancelled')
            ->get();

        $details  =   DB::table('orders')
            ->join('store_orders', 'orders.cart_id', '=', 'store_orders.order_cart_id')
            ->where('store_orders.store_approval', 1)
            ->get();

        return view('admin.all_orders.cancelled', compact('title', 'logo', 'ord', 'details', 'admin'));
    }

    public function admin_pen_orders(Request $request)
    {
        $title = "Pending Order section";
        $admin_email = Session::get('bamaAdmin');
        $admin = DB::table('admin')
            ->where('admin_email', $admin_email)
            ->first();
        $logo = DB::table('tbl_web_setting')
            ->where('set_id', '1')
            ->first();

        $ord = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.user_id')
            ->orderBy('orders.order_id', 'DESC')
            ->where('orders.order_status', 'Pending')
            ->where('orders.payment_method', '!=', null)
            //  ->orWhere('orders.order_status', 'Out_For_Delivery')
            //  ->orWhere('orders.order_status', 'Confirmed')
            //  ->orWhere('orders.order_status', 'Accepted_By_Delivery_Agent')
            ->get();

        $details  =   DB::table('orders')
            ->join('store_orders', 'orders.cart_id', '=', 'store_orders.order_cart_id')
            ->where('store_orders.store_approval', 1)
            ->get();

        return view('admin.all_orders.pending', compact('title', 'logo', 'ord', 'details', 'admin'));
    }


    public function admin_store_orders(Request $request)
    {
        $title = "Store Order section";
        $id = $request->id;
        $store = DB::table('store')
            ->where('store_id', $id)
            ->first();
        $admin_email = Session::get('bamaAdmin');
        $admin = DB::table('admin')
            ->where('admin_email', $admin_email)
            ->first();
        $logo = DB::table('tbl_web_setting')
            ->where('set_id', '1')
            ->first();

        $ord = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.user_id')
            ->where('orders.store_id', $store->store_id)
            ->orderBy('orders.delivery_date', 'ASC')
            ->where('order_status', '!=', 'completed')
            ->get();

        $details  =   DB::table('orders')
            ->join('store_orders', 'orders.cart_id', '=', 'store_orders.order_cart_id')
            ->where('orders.store_id', $id)
            ->where('store_orders.store_approval', 1)
            ->get();

        return view('admin.store.orders', compact('title', 'logo', 'ord', 'store', 'details', 'admin'));
    }



    public function admin_dboy_orders(Request $request)
    {
        $title = "Delivery Boy Order section";
        $id = $request->id;
        $dboy = DB::table('delivery_boy')
            ->where('dboy_id', $id)
            ->first();
        $admin_email = Session::get('bamaAdmin');
        $admin = DB::table('admin')
            ->where('admin_email', $admin_email)
            ->first();
        $logo = DB::table('tbl_web_setting')
            ->where('set_id', '1')
            ->first();

        $date = date('Y-m-d');
        $nearbydboy = DB::table('delivery_boy')
            ->leftJoin('orders', 'delivery_boy.dboy_id', '=', 'orders.dboy_id')
            ->select("delivery_boy.boy_name", "delivery_boy.dboy_id", "delivery_boy.lat", "delivery_boy.lng", "delivery_boy.boy_city", DB::raw("Count(orders.order_id)as count"), DB::raw("6371 * acos(cos(radians(" . $dboy->lat . ")) 
                * cos(radians(delivery_boy.lat)) 
                * cos(radians(delivery_boy.lng) - radians(" . $dboy->lng . ")) 
                + sin(radians(" . $dboy->lat . ")) 
                * sin(radians(delivery_boy.lat))) AS distance"))
            ->groupBy("delivery_boy.boy_name", "delivery_boy.dboy_id", "delivery_boy.lat", "delivery_boy.lng", "delivery_boy.boy_city")
            ->where('delivery_boy.boy_city', $dboy->boy_city)
            ->where('delivery_boy.dboy_id', '!=', $dboy->dboy_id)
            ->orderBy('count')
            ->orderBy('distance')
            ->get();


        $ord = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.user_id')
            ->where('orders.dboy_id', $dboy->dboy_id)
            ->orderBy('orders.delivery_date', 'ASC')
            ->where('order_status', '!=', 'completed')
            ->paginate(10);

        $details  =   DB::table('orders')
            ->join('store_orders', 'orders.cart_id', '=', 'store_orders.order_cart_id')
            ->where('orders.dboy_id', $id)
            ->where('store_orders.store_approval', 1)
            ->get();

        return view('admin.d_boy.orders', compact('title', 'logo', 'ord', 'dboy', 'details', 'admin', 'nearbydboy'));
    }



    public function store_cancelled(Request $request)
    {
        $title = "Store Cancelled Orders";
        $admin_email = Session::get('bamaAdmin');
        $admin = DB::table('admin')
            ->where('admin_email', $admin_email)
            ->first();
        $logo = DB::table('tbl_web_setting')
            ->where('set_id', '1')
            ->first();

        $ord = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.user_id')
            ->join('address', 'orders.address_id', '=', 'address.address_id')
            ->orderBy('orders.delivery_date', 'ASC')
            ->where('order_status', '!=', 'completed')
            ->where('order_status', '!=', 'cancelled')
            ->where('payment_method', '!=', NULL)
            ->where('order_status', '=', 'Rejected_By_Vendor')
            //  ->where('store_id', 0)
            ->paginate(10000);


        $nearbystores = DB::table('store')
            ->get();


        $details  =   DB::table('orders')
            ->join('store_orders', 'orders.cart_id', '=', 'store_orders.order_cart_id')
            ->where('store_orders.store_approval', 1)
            ->get();

        return view('admin.store.cancel_orders', compact('title', 'logo', 'ord', 'details', 'admin', 'nearbystores'));
    }


    public function assignstore(Request $request)
    {
        $title = "Store Cancelled Orders";
        $cart_id = $request->id;
        $store = $request->store;
        $admin_email = Session::get('bamaAdmin');
        $admin = DB::table('admin')
            ->where('admin_email', $admin_email)
            ->first();
        $logo = DB::table('tbl_web_setting')
            ->where('set_id', '1')
            ->first();

        $ord = DB::table('orders')
            ->where('cart_id', $cart_id)
            ->update(['store_id' => $store, 'cancel_by_store' => 0]);


        return redirect()->back()->withSuccess('Assigned to store successfully');
    }

    public function assigndboy(Request $request)
    {
        $cart_id = $request->id;
        $d_boy = $request->d_boy;
        $admin_email = Session::get('bamaAdmin');
        $admin = DB::table('admin')
            ->where('admin_email', $admin_email)
            ->first();
        $logo = DB::table('tbl_web_setting')
            ->where('set_id', '1')
            ->first();

        $ord = DB::table('orders')
            ->where('cart_id', $cart_id)
            ->update(['dboy_id' => $d_boy]);


        return redirect()->back()->withSuccess('Assigned to Another Delivery Boy Successfully');
    }


    public function rejectorder(Request $request)
    {
        $cart_id = $request->id;
        $ord = DB::table('orders')
            ->where('cart_id', $cart_id)
            ->first();
        $item_total_price = $ord->rem_price;
        $user = DB::table('users')
            ->where('user_id', $ord->user_id)
            ->first();
        $user_id = $ord->user_id;
        $wall = $user->wallet;
        $bywallet = $ord->paid_by_wallet;
        if ($ord->payment_method != 'COD' || $ord->payment_method != 'cod' || $ord->payment_method != 'Cod') {
            $newwallet = $wall + $item_total_price + $bywallet;
            $update = DB::table('users')
                ->where('user_id', $ord->user_id)
                ->update(['wallet' => $newwallet]);
        } else {
            $newwallet = $wall + $bywallet;
            $update = DB::table('users')
                ->where('user_id', $ord->user_id)
                ->update(['wallet' => $newwallet]);
        }

        $cause = $request->cause;

        $checknotificationby = DB::table('notificationby')
            ->where('user_id', $user->user_id)
            ->first();
        if ($checknotificationby->sms == 1) {
            $sendmsg = $this->sendrejectmsg($cause, $user, $cart_id);
        }
        if ($checknotificationby->email == 1) {
            $sendmail = $this->sendrejectmail($cause, $user, $cart_id);
        }
        if ($checknotificationby->app == 1) {
            //////send notification to user//////////
            $notification_title = "Sorry! we are cancelling your order";
            $notification_text = 'Hello ' . $user->user_name . ', We are cancelling your order (' . $cart_id . ') due to following reason:  ' . $cause;
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

        $ord = DB::table('orders')
            ->where('cart_id', $cart_id)
            ->update([
                'cancelling_reason' => "Cancelled by Admin due to the following reason: " . $cause,
                'order_status' => "cancelled"
            ]);
        return redirect()->back()->withSuccess('Order Rejected Successfully');
    }
}
