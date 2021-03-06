<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
  public function list(Request $request)
  {
    $title = "Users Management List";
    $admin_email = Session::get('bamaAdmin');
    $admin = DB::table('admin')
        ->where('admin_email', $admin_email)
        ->first();
    if (!Helper::hasRight($admin->id, 'customers', 'View')) {
        return abort(403);
    }
    $logo = DB::table('tbl_web_setting')
      ->where('set_id', '1')
      ->first();
    $users = DB::table('users')
      ->orderBy('reg_date', 'desc')
      ->get();

    return view('admin.user.list', compact('title', "admin", "logo", "users"));
  }

  public function block(Request $request)
  {
    $admin_email = Session::get('bamaAdmin');
    $admin = DB::table('admin')
        ->where('admin_email', $admin_email)
        ->first();
    if (!Helper::hasRight($admin->id, 'customers', 'Edit')) {
        return abort(403);
    }
    $user_id = $request->id;
    $users = DB::table('users')
      ->where('user_id', $user_id)
      ->update(['block' => 1]);
    if ($users) {
      return redirect()->back()->withSuccess('User Blocked Successfully');
    } else {
      return redirect()->back()->withErrors('Something Wents Wrong');
    }
  }

  public function unblock(Request $request)
  {
    $admin_email = Session::get('bamaAdmin');
    $admin = DB::table('admin')
        ->where('admin_email', $admin_email)
        ->first();
    if (!Helper::hasRight($admin->id, 'customers', 'View')) {
        return abort(403);
    }

    $user_id = $request->id;
    $users = DB::table('users')
      ->where('user_id', $user_id)
      ->update(['block' => 2]);

    if ($users) {
      return redirect()->back()->withSuccess('User Unblocked Successfully');
    } else {
      return redirect()->back()->withErrors('Something Wents Wrong');
    }
  }

  public function del_user(Request $request)
  {
    $admin_email = Session::get('bamaAdmin');
    $admin = DB::table('admin')
        ->where('admin_email', $admin_email)
        ->first();
    if (!Helper::hasRight($admin->id, 'customers', 'Delete')) {
        return abort(403);
    }

    $user_id = $request->id;
    $users = DB::table('users')
      ->where('user_id', $user_id)
      ->delete();

    if ($users) {
      $address = DB::table('address')
        ->where('user_id', $user_id)
        ->delete();
      $orders = DB::table('orders')
        ->where('user_id', $user_id)
        ->delete();

      return redirect()->back()->withSuccess('User deleted Successfully');
    } else {
      return redirect()->back()->withErrors('Something Wents Wrong');
    }
  }
}
