<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Traits\SendMail;
use App\Traits\SendSms;
use App\User;
use App\WalletHistory;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;

class WalletController extends Controller
{
    use SendSms;
    use SendMail;

    public function createWalletOrder(Request $request)
    {
        if($request->payment_mode == 'razorpay'){

            if($request->amount==null || $request->amount < 1){
                return array('status' => '0', 'message' => 'The amount must be atleast INR 1.00');
            }

            $user = DB::table('users')->where('user_id',$request->user_id)->first();
            if(empty($user)){
                return array('status' => '0', 'message' => 'Invalid User');
            }

            $wallet_balance = $request->amount;
            $wallet = new WalletHistory();
            $wallet->user_id = $request->user_id;
            $wallet->amount = $wallet_balance;
            $wallet->trans_type = "recharge";
            $wallet->status = 'pending';
            $wallet->save();

            $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
            $orderData = [
                'receipt'         => 'Wallet Recharge #'.$wallet->id,
                'amount'          => $wallet_balance * 100,
                'currency'        => 'INR'
            ];
            $razorpayOrder = $api->order->create($orderData);

            $wallet->payment_order_id = $razorpayOrder->id;
            $wallet->save();

            return array('status' => '1', 'message' => 'Wallet Order Created','data'=>$wallet);
        }else{
            return array('status' => '0', 'message' => 'Invalid Payment Mode');
        }
    }

    public function confirmWalletRecharge(Request $request)
    {
        if(empty($request->recharge_id)){
            return array('status' => '0', 'message' => 'Invalid Recharge');
        }
        $rechargeId = $request->recharge_id;
        $recharge = WalletHistory::find($rechargeId);
        if(empty($recharge)){
            return array('status' => '0', 'message' => 'Invalid Recharge');
        }
        if($recharge->status == 'success'){
            return array('status' => '1', 'message' => 'Recharge already done');
        }

        $recharge_amount = $recharge->amount;
        $curr = DB::table('currency')
            ->first();
        $recharge_status = $request->recharge_status;
        $user_id = $request->user_id;
        $wallet_amt = DB::table('users')
            ->select('wallet')
            ->where('user_id', $user_id)
            ->first();

        $date_of_recharge = carbon::now();
        $wallet_balance = $wallet_amt->wallet;
        $added = $recharge_amount + $wallet_balance;
        $currentDate = date('Y-m-d');
        $ph = DB::table('users')
            ->select('user_phone', 'user_email', 'user_name')
            ->where('user_id', $user_id)
            ->first();
        $user_phone = $ph->user_phone;
        $user_email = $ph->user_email;
        $user_name = $ph->user_name;

        if ($recharge_status == 'success') {
            if(!empty($request->payment_id)){
                $paymentId = $request->payment_id;
                $payble_price = $recharge->amount;
                $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
                // $payment = $api->payment->fetch($paymentId);
                $rp_order = $api->order->fetch($recharge->payment_order_id)->payments();
                if(empty($rp_order)){
                    return array('status'=>'0', 'message'=>'Invalid Payment');
                }
                if($rp_order->items[0]->status != 'failed' && $payble_price*100 == $rp_order->items[0]->amount){
                    $recharge->payment_id = $paymentId;
                    $recharge->status = 'success';
                    $recharge->save();
                }else{
                    return array('status'=>'0', 'message'=>'Payment is failed');
                }
            }else{
                return array('status'=>'0', 'message'=>'Payment is failed');
            }
            $wallet_amt = DB::table('users')
                ->where('user_id', $user_id)
                ->update(['wallet' => $added]);

            $insert =  DB::table('wallet_recharge_history')
                ->insert([
                    'user_id' => $user_id,
                    'amount' => $recharge_amount,
                    'date_of_recharge' => $date_of_recharge,
                    'recharge_status' => $recharge_status
                ]);

            if ($insert) {
                // start sms
                $sms = DB::table('notificationby')
                    ->select('sms')
                    ->where('user_id', $user_id)
                    ->first();
                $sms_status = $sms->sms;
                $sms_api_key =  DB::table('msg91')
                    ->select('api_key', 'sender_id')
                    ->first();
                $api_key = $sms_api_key->api_key;
                $sender_id = $sms_api_key->sender_id;
                if ($sms_status == 1) {
                    $rechargeSms = $this->rechargesms($curr, $user_name, $recharge_amount, $user_phone);
                }
                // end sms



                /////send mail
                $email = DB::table('notificationby')
                    ->select('email')
                    ->where('user_id', $user_id)
                    ->first();
                $email_status = $email->email;
                if ($email_status == 1) {

                    $rechargeMail = $this->rechargeMail($user_id, $user_name, $user_email, $user_phone, $recharge_amount);
                }
                ////end send mail 

                $message = array('status' => '1', 'message' => 'wallet recharged successfully');
                return $message;
            }
        } else {
            $insert =  DB::table('wallet_recharge_history')
                ->insert([
                    'user_id' => $user_id,
                    'amount' => $recharge_amount,
                    'date_of_recharge' => $date_of_recharge,
                    'recharge_status' => 'failed'
                ]);
            $message = array('status' => '0', 'message' => 'Failed! try again', 'data' => []);
            return $message;
        }
    }

    public function add_credit(Request $request)
    {
        $add_to_wallet = $request->amount;
        $curr = DB::table('currency')
            ->first();
        $recharge_status = $request->recharge_status;
        $user_id = $request->user_id;
        $wallet_amt = DB::table('users')
            ->select('wallet')
            ->where('user_id', $user_id)
            ->first();

        $date_of_recharge = carbon::now();
        $amount = $wallet_amt->wallet;
        $added = $add_to_wallet + $amount;
        $currentDate = date('Y-m-d');
        $ph = DB::table('users')
            ->select('user_phone', 'user_email', 'user_name')
            ->where('user_id', $user_id)
            ->first();
        $user_phone = $ph->user_phone;
        $user_email = $ph->user_email;
        $user_name = $ph->user_name;


        if ($recharge_status == 'success') {
            $wallet_amt = DB::table('users')
                ->where('user_id', $user_id)
                ->update(['wallet' => $added]);

            $insert =  DB::table('wallet_recharge_history')
                ->insert([
                    'user_id' => $user_id,
                    'amount' => $add_to_wallet,
                    'date_of_recharge' => $date_of_recharge,
                    'recharge_status' => $recharge_status
                ]);

            if ($insert) {
                // start sms
                $sms = DB::table('notificationby')
                    ->select('sms')
                    ->where('user_id', $user_id)
                    ->first();
                $sms_status = !empty($sms)?$sms->sms:0;
                $sms_api_key =  DB::table('msg91')
                    ->select('api_key', 'sender_id')
                    ->first();
                $api_key = $sms_api_key->api_key;
                $sender_id = $sms_api_key->sender_id;
                if ($sms_status == 1) {
                    $rechargeSms = $this->rechargesms($curr, $user_name, $add_to_wallet, $user_phone);
                }
                // end sms



                /////send mail
                $email = DB::table('notificationby')
                    ->select('email')
                    ->where('user_id', $user_id)
                    ->first();
                $email_status = !empty($email)?$email->email:0;
                if ($email_status == 1) {
                    $rechargeMail = $this->rechargeMail($user_id, $user_name, $user_email, $user_phone, $add_to_wallet);
                }
                ////end send mail 

                $message = array('status' => '1', 'message' => 'wallet recharged successfully');
                return $message;
            }
        } else {
            $insert =  DB::table('wallet_recharge_history')
                ->insert([
                    'user_id' => $user_id,
                    'amount' => $add_to_wallet,
                    'date_of_recharge' => $date_of_recharge,
                    'recharge_status' => 'failed'
                ]);
            $message = array('status' => '0', 'message' => 'Failed! try again', 'data' => []);
            return $message;
        }
    }

    public function walletamount(Request $request)
    {
        $user_id = $request->user_id;
        $wallet = DB::table('users')
            ->select('wallet')
            ->where('user_id', $user_id)
            ->first();
        $wallet_amt = $wallet->wallet;

        if ($wallet) {
            $message = array('status' => '1', 'message' => 'Wallet_amount', 'data' => $wallet_amt);
            return $message;
        } else {
            $message = array('status' => '0', 'message' => 'No user Found', 'data' => $wallet_amt);
            return $message;
        }
    }

    public function totalbill(Request $request)
    {
        $user_id = $request->user_id;
        $wallet_amt = DB::table('users')
            ->select('wallet')
            ->where('user_id', $user_id)
            ->first();

        $current_amount = $wallet_amt->wallet;
        $last = DB::table('wallet_recharge_history')
            ->select('amount', 'date_of_recharge')
            ->where('user_id', $user_id)
            ->where('recharge_status', 'success')
            ->orderBy('wallet_recharge_history_id', 'desc')
            ->first();

        if ($last) {
            $lastrecharge = $last->amount;
            $date = $last->date_of_recharge;
            $lastrechargedate = date('Y-m-d', strtotime($date));
            $orders = DB::table('orders')
                ->where('user_id', $user_id)
                ->where('delivery_date', '>', $lastrechargedate)
                ->SUM('total_price');

            $balanceafrecharge = $current_amount + $orders;
            $message = array('status' => '1', 'message' => 'data found', 'billafterrecharge' => $orders, 'balanceafterrecharge' => $balanceafrecharge, 'lastrecharge' => $lastrecharge, 'currentamount' => $current_amount);
            return $message;
        } else {
            $message = array('status' => '0', 'message' => 'data found', 'billafterrecharge' => 0, 'balanceafterrecharge' => 0, 'lastrecharge' => 0, 'currentamount' => $current_amount);
            return $message;
        }
    }


    public function show_recharge_history(Request $request)
    {
        $user_id = $request->user_id;
        $show =  DB::table('wallet_recharge_history')
            ->join('users', 'wallet_recharge_history.user_id', '=', 'users.user_id')
            ->where('users.user_id', $user_id)
            ->orderBy('wallet_recharge_history.wallet_recharge_history_id', 'DESC')
            ->get();

        if ($show) {
            $message = array('status' => '1', 'message' => 'data found', 'data' => $show);
            return $message;
        } else {
            $message = array('status' => '0', 'message' => 'something went wrong', 'data' => []);
            return $message;
        }
    }
}
