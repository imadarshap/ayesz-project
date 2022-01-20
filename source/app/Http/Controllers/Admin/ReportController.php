<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ReportController extends Controller
{

	public function report(Request $request)
	{
		$title = "Vendor Payment Report";
		$admin_email = Session::get('bamaAdmin');
		$admin = DB::table('admin')
			->where('admin_email', $admin_email)
			->first();
		if (!Helper::hasRight($admin->id, 'reports', 'View') && !Helper::hasRight($admin->id, 'report_by_vendor_payment', 'View')) {
			return abort(403);
		}
		$logo = DB::table('tbl_web_setting')
			->where('set_id', '1')
			->first();
		// $stores = DB::table('store')->get();
		// $delivery_boys = DB::table('delivery_boy')->get();
		DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
		$city = $request->city;
		$stores_selected = $request->stores;
		$dboys_selected = $request->delivery_agents;
		$fromDate = $request->fromdate;
		$toDate = $request->todate;
		$orders = DB::table('orders')->join('store', 'store.store_id', 'orders.store_id')
			->leftJoin('users', function ($join) {
				$join->on('orders.user_id', 'users.user_id')
					->select('user_name');
			})->leftJoin('delivery_boy', 'delivery_boy.dboy_id', 'orders.dboy_id')
			->leftJoin('store_orders', 'store_orders.order_cart_id', '=', 'orders.cart_id')
			->where('orders.payment_method', '!=', NULL)
			//  ->where('orders.order_status','Completed')
			->where('orders.order_status', '!=', 'Rejected_By_Vendor')
			->where('orders.order_status', '!=', 'Cancelled')
			//->select('*',DB::raw('((orders.price_without_delivery/100)*store.admin_share) as commission'));
			//	->select('*',DB::raw('((orders.price_without_delivery/100)*store.admin_share) as commission'));
			->select('*', DB::raw('((orders.price_without_delivery*(store.admin_share/(100 + store.admin_share)))) as commission'));

		//	->select(DB::raw('((store_orders.price_without_delivery *(store.admin_share/100 + store.admin_share))) as commission'))

		$stores = array();
		$dboys = array();

		if (!empty($city)) {
			$orders = $orders->where('store.city', $city);

			if (!empty($stores_selected) && !empty($dboys_selected)) {
				$orders = $orders->whereIn('orders.store_id', $stores_selected)
					->whereIn('orders.dboy_id', $dboys_selected);
			} else if (!empty($stores_selected) && empty($dboys_selected)) {
				$orders = $orders->whereIn('orders.store_id', $stores_selected);
			} else if (empty($stores_selected) && !empty($dboys_selected)) {
				$orders = $orders->whereIn('orders.dboy_id', $dboys_selected);
			}

			if (!empty($fromDate) && !empty($toDate)) {
				$orders = $orders->where('orders.order_date', '>=', $fromDate)
					->where('orders.order_date', '<=', $toDate);
			} else if (!empty($fromDate) && empty($toDate)) {
				$orders = $orders->where('orders.order_date', $fromDate);
			} else if (empty($fromDate) && !empty($toDate)) {
				$orders = $orders->where('orders.order_date', '<=', $toDate);
			}

			$stores = DB::table('store')->where('city', $city)->get();
			$dboys = DB::table('delivery_boy')->where('boy_city', $city)->get();
			$orders = $orders->groupby('cart_id')->orderby('orders.order_date', 'ASC')->get();
		} else {
			$orders = array();
		}

		$allowedCities = explode(',',$admin->locations);
        $cities = DB::table('city')->whereIn('city_name',$allowedCities)->get();

		$orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->count();
		$completed_orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->where('order_status', 'Completed')->count();
		$pending_orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->where('order_status', 'Pending')->count();
		$cancelled_orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->where('order_status', 'Cancelled')->count();

		$admin_earning = DB::table('store_orders')
			->leftJoin('orders', 'store_orders.order_cart_id', '=', 'orders.cart_id')
			->leftJoin('store_products', function ($join) {
				$join->on('store_orders.varient_id', '=', 'store_products.varient_id')
					->select('store_id');
			})
			->leftJoin('store', function ($join) {
				$join->on('store_products.store_id', '=', 'store.store_id')
					->select('admin_share');
			})
			->where('orders.order_status', 'Completed')
			->groupby('store_orders.store_order_id')
			//	 ->select(DB::raw('((store_orders.price/100)*store.admin_share) as earning'))
			->select(DB::raw('((store_orders.price *(store.admin_share/100 + store.admin_share))) as earning'))
			->get()->sum('earning');
		$order_total = DB::table('orders')
			->where('orders.order_status', 'Completed')
			->sum('total_price');

		// $orders=DB::table('orders')
		// ->where('orders.order_status','Completed')
		// ->get();

		$report = array(
			'orders_count' => $orders_count,
			'completed_orders_count' => $completed_orders_count,
			'pending_orders_count' => $pending_orders_count,
			'cancelled_orders_count' => $cancelled_orders_count,
			'orders' => $orders,
			'order_total' => $order_total,
			'admin_earning' => $admin_earning
		);


		return view('admin.report.index', compact('title', 'admin', 'logo', 'report', 'cities', 'request', 'stores', 'dboys'));
	}

	public function reportByPaymentMode(Request $request)
	{
		$title = "Report By Payment Mode";
		$admin_email = Session::get('bamaAdmin');
		$admin = DB::table('admin')
			->where('admin_email', $admin_email)
			->first();
		if (!Helper::hasRight($admin->id, 'reports', 'View') && !Helper::hasRight($admin->id, 'report_by_payment_mode', 'View')) {
			return abort(403);
		}
		$logo = DB::table('tbl_web_setting')
			->where('set_id', '1')
			->first();
		// $stores = DB::table('store')->get();
		// $delivery_boys = DB::table('delivery_boy')->get();
		DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
		$city = $request->city;


		$paymentModes = $request->payment_modes;
		$fromDate = $request->fromdate;
		$toDate = $request->todate;
		$orders = DB::table('orders')->join('store', 'store.store_id', 'orders.store_id')
			->leftJoin('users', function ($join) {
				$join->on('orders.user_id', 'users.user_id')
					->select('user_name');
			})->leftJoin('delivery_boy', 'delivery_boy.dboy_id', 'orders.dboy_id')
			->leftJoin('store_orders', 'store_orders.order_cart_id', '=', 'orders.cart_id')
			->where('orders.payment_method', '!=', NULL)
			->select('*', DB::raw('((orders.price_without_delivery/100)*store.admin_share) as commission'));

		if (!empty($city)) {
			$orders = $orders->where('store.city', $city);
			if (!empty($paymentModes)) {
				$orders = $orders->whereIn('orders.payment_method', $paymentModes);
			}
			if (!empty($fromDate) && !empty($toDate)) {
				$orders = $orders->where('orders.order_date', '>=', $fromDate)
					->where('orders.order_date', '<=', $toDate);
			} else if (!empty($fromDate) && empty($toDate)) {
				$orders = $orders->where('orders.order_date', $fromDate);
			} else if (empty($fromDate) && !empty($toDate)) {
				$orders = $orders->where('orders.order_date', '<=', $toDate);
			}
			$orders = $orders->groupby('cart_id')->orderby('orders.order_date', 'ASC')->get();
		} else {
			$orders = array();
		}


		$allowedCities = explode(',',$admin->locations);
        $cities = DB::table('city')->whereIn('city_name',$allowedCities)->get();


		$orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->count();
		$completed_orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->where('order_status', 'Completed')->count();
		$pending_orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->where('order_status', 'Pending')->count();
		$cancelled_orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->where('order_status', 'Cancelled')->count();

		$admin_earning = DB::table('store_orders')
			->leftJoin('orders', 'store_orders.order_cart_id', '=', 'orders.cart_id')
			->leftJoin('store_products', function ($join) {
				$join->on('store_orders.varient_id', '=', 'store_products.varient_id')
					->select('store_id');
			})
			->leftJoin('store', function ($join) {
				$join->on('store_products.store_id', '=', 'store.store_id')
					->select('admin_share');
			})
			->where('orders.order_status', 'Completed')
			->groupby('store_orders.store_order_id')
			->select(DB::raw('((store_orders.price/100)*store.admin_share) as earning'))
			->get()->sum('earning');
		$order_total = DB::table('orders')
			->where('orders.order_status', 'Completed')
			->sum('total_price');

		// $orders=DB::table('orders')
		// ->where('orders.order_status','Completed')
		// ->get();

		$report = array(
			'orders_count' => $orders_count,
			'completed_orders_count' => $completed_orders_count,
			'pending_orders_count' => $pending_orders_count,
			'cancelled_orders_count' => $cancelled_orders_count,
			'orders' => $orders,
			'order_total' => $order_total,
			'admin_earning' => $admin_earning
		);


		return view('admin.report.report_by_payment_mode', compact('title', 'admin', 'logo', 'report', 'cities', 'request'));
	}

	public function reportByVendor(Request $request)
	{
		$title = "Report By Vendor";
		$admin_email = Session::get('bamaAdmin');
		$admin = DB::table('admin')
			->where('admin_email', $admin_email)
			->first();
		if (!Helper::hasRight($admin->id, 'reports', 'View') && !Helper::hasRight($admin->id, 'report_by_vendor_orders', 'View')) {
			return abort(403);
		}
		$logo = DB::table('tbl_web_setting')
			->where('set_id', '1')
			->first();
		// $stores = DB::table('store')->get();
		// $delivery_boys = DB::table('delivery_boy')->get();
		DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
		$city = $request->city;
		$paymentModes = $request->payment_modes;
		$stores_selected = $request->stores;
		$fromDate = $request->fromdate;
		$toDate = $request->todate;
		$orders = DB::table('orders')->join('store', 'store.store_id', 'orders.store_id')
			->leftJoin('users', function ($join) {
				$join->on('orders.user_id', 'users.user_id')
					->select('user_name');
			})->leftJoin('delivery_boy', 'delivery_boy.dboy_id', 'orders.dboy_id')
			->leftJoin('store_orders as b', function ($join) {
				$join->on('b.order_cart_id', '=', 'orders.cart_id')
					->groupby('order_cart_id');
			})
			->where('orders.payment_method', '!=', NULL)
			->select('*', DB::raw('((orders.price_without_delivery/100)*store.admin_share) as commission'), DB::raw('SUM(qty) as qtysum'));

		$stores = array();

		if (!empty($city)) {
			$orders = $orders->where('store.city', $city);
			if (!empty($paymentModes)) {
				$orders = $orders->whereIn('orders.payment_method', $paymentModes);
			}
			if (!empty($stores_selected)) {
				$orders = $orders->whereIn('orders.store_id', $stores_selected);
			}

			if (!empty($fromDate) && !empty($toDate)) {
				$orders = $orders->where('orders.order_date', '>=', $fromDate)
					->where('orders.order_date', '<=', $toDate);
			} else if (!empty($fromDate) && empty($toDate)) {
				$orders = $orders->where('orders.order_date', $fromDate);
			} else if (empty($fromDate) && !empty($toDate)) {
				$orders = $orders->where('orders.order_date', '<=', $toDate);
			}

			$stores = DB::table('store')->where('city', $city)->get();
			$orders = $orders->groupby('cart_id')->orderby('orders.order_date', 'ASC')->get();
		} else {
			$orders = array();
		}

		$allowedCities = explode(',',$admin->locations);
        $cities = DB::table('city')->whereIn('city_name',$allowedCities)->get();

		$orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->count();
		$completed_orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->where('order_status', 'Completed')->count();
		$pending_orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->where('order_status', 'Pending')->count();
		$cancelled_orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->where('order_status', 'Cancelled')->count();

		$admin_earning = DB::table('store_orders')
			->leftJoin('orders', 'store_orders.order_cart_id', '=', 'orders.cart_id')
			->leftJoin('store_products', function ($join) {
				$join->on('store_orders.varient_id', '=', 'store_products.varient_id')
					->select('store_id');
			})
			->leftJoin('store', function ($join) {
				$join->on('store_products.store_id', '=', 'store.store_id')
					->select('admin_share');
			})
			->where('orders.order_status', 'Completed')
			->groupby('store_orders.store_order_id')
			->select(DB::raw('((store_orders.price/100)*store.admin_share) as earning'))
			->get()->sum('earning');
		$order_total = DB::table('orders')
			->where('orders.order_status', 'Completed')
			->sum('total_price');

		// $orders=DB::table('orders')
		// ->where('orders.order_status','Completed')
		// ->get();

		$report = array(
			'orders_count' => $orders_count,
			'completed_orders_count' => $completed_orders_count,
			'pending_orders_count' => $pending_orders_count,
			'cancelled_orders_count' => $cancelled_orders_count,
			'orders' => $orders,
			'order_total' => $order_total,
			'admin_earning' => $admin_earning
		);


		return view('admin.report.report_by_vendor', compact('title', 'admin', 'logo', 'report', 'cities', 'request', 'stores'));
	}


	public function report_by_product_list(Request $request)
	{

		$title = "Report By Product List";
		$admin_email = Session::get('bamaAdmin');
		$admin = DB::table('admin')
			->where('admin_email', $admin_email)
			->first();
		if (!Helper::hasRight($admin->id, 'reports', 'View') && !Helper::hasRight($admin->id, 'report_by_products', 'View')) {
			return abort(403);
		}
		$logo = DB::table('tbl_web_setting')
			->where('set_id', '1')
			->first();
		// $stores = DB::table('store')->get();
		// $delivery_boys = DB::table('delivery_boy')->get();
		DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
		$city = $request->city;
		$paymentModes = $request->payment_modes;
		$stores_selected = $request->stores;
		$fromDate = $request->fromdate;
		$toDate = $request->todate;
		$orders = DB::table('orders')->join('store', 'store.store_id', 'orders.store_id')
			->leftJoin('users', function ($join) {
				$join->on('orders.user_id', 'users.user_id')
					->select('user_name');
			})->leftJoin('delivery_boy', 'delivery_boy.dboy_id', 'orders.dboy_id')
			->leftJoin('store_orders as b', function ($join) {
				$join->on('b.order_cart_id', '=', 'orders.cart_id')
					->groupby('order_cart_id');
			})
			->where('orders.payment_method', '!=', NULL)
			->select('*', DB::raw('((orders.price_without_delivery/100)*store.admin_share) as commission'), DB::raw('SUM(qty) as qtysum'));

		$stores = array();

		if (!empty($city)) {
			$orders = $orders->where('store.city', $city);
			if (!empty($paymentModes)) {
				$orders = $orders->whereIn('orders.payment_method', $paymentModes);
			}
			if (!empty($stores_selected)) {
				$orders = $orders->whereIn('orders.store_id', $stores_selected);
			}

			if (!empty($fromDate) && !empty($toDate)) {
				$orders = $orders->where('orders.order_date', '>=', $fromDate)
					->where('orders.order_date', '<=', $toDate);
			} else if (!empty($fromDate) && empty($toDate)) {
				$orders = $orders->where('orders.order_date', $fromDate);
			} else if (empty($fromDate) && !empty($toDate)) {
				$orders = $orders->where('orders.order_date', '<=', $toDate);
			}

			$stores = DB::table('store')->where('city', $city)->get();
			$orders = $orders->groupby('cart_id')->orderby('orders.order_date', 'ASC')->get();
		} else {
			$orders = array();
		}

		$allowedCities = explode(',',$admin->locations);
        $cities = DB::table('city')->whereIn('city_name',$allowedCities)->get();


		$orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->count();
		$completed_orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->where('order_status', 'Completed')->count();
		$pending_orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->where('order_status', 'Pending')->count();
		$cancelled_orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->where('order_status', 'Cancelled')->count();

		$admin_earning = DB::table('store_orders')
			->leftJoin('orders', 'store_orders.order_cart_id', '=', 'orders.cart_id')
			->leftJoin('store_products', function ($join) {
				$join->on('store_orders.varient_id', '=', 'store_products.varient_id')
					->select('store_id');
			})
			->leftJoin('store', function ($join) {
				$join->on('store_products.store_id', '=', 'store.store_id')
					->select('admin_share');
			})
			->where('orders.order_status', 'Completed')
			->groupby('store_orders.store_order_id')
			->select(DB::raw('((store_orders.price/100)*store.admin_share) as earning'))
			->get()->sum('earning');
		$order_total = DB::table('orders')
			->where('orders.order_status', 'Completed')
			->sum('total_price');

		// $orders=DB::table('orders')
		// ->where('orders.order_status','Completed')
		// ->get();

		$report = array(
			'orders_count' => $orders_count,
			'completed_orders_count' => $completed_orders_count,
			'pending_orders_count' => $pending_orders_count,
			'cancelled_orders_count' => $cancelled_orders_count,
			'orders' => $orders,
			'order_total' => $order_total,
			'admin_earning' => $admin_earning
		);

		$adminTopApp =  DB::table('categories')->get();


		return view('admin.report.report_by_product_list', compact('title', 'admin', 'logo', 'report', 'cities', 'request', 'stores', 'adminTopApp'));
	}

	public function report_by_vindoe_list(Request $request)
	{

		$title = "Report By Vendor List";
		$admin_email = Session::get('bamaAdmin');
		$admin = DB::table('admin')
			->where('admin_email', $admin_email)
			->first();
		if (!Helper::hasRight($admin->id, 'reports', 'View') && !Helper::hasRight($admin->id, 'report_by_vendors', 'View')) {
			return abort(403);
		}
		$logo = DB::table('tbl_web_setting')
			->where('set_id', '1')
			->first();
		// $stores = DB::table('store')->get();
		// $delivery_boys = DB::table('delivery_boy')->get();
		DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
		$city = $request->city;
		$paymentModes = $request->payment_modes;
		$stores_selected = $request->stores;
		$fromDate = $request->fromdate;
		$toDate = $request->todate;
		$orders = DB::table('orders')->join('store', 'store.store_id', 'orders.store_id')
			->leftJoin('users', function ($join) {
				$join->on('orders.user_id', 'users.user_id')
					->select('user_name');
			})->leftJoin('delivery_boy', 'delivery_boy.dboy_id', 'orders.dboy_id')
			->leftJoin('store_orders as b', function ($join) {
				$join->on('b.order_cart_id', '=', 'orders.cart_id')
					->groupby('order_cart_id');
			})
			->where('orders.payment_method', '!=', NULL)
			->select('*', DB::raw('((orders.price_without_delivery/100)*store.admin_share) as commission'), DB::raw('SUM(qty) as qtysum'));

		$stores = array();

		if (!empty($city)) {
			$orders = $orders->where('store.city', $city);
			if (!empty($paymentModes)) {
				$orders = $orders->whereIn('orders.payment_method', $paymentModes);
			}
			if (!empty($stores_selected)) {
				$orders = $orders->whereIn('orders.store_id', $stores_selected);
			}

			if (!empty($fromDate) && !empty($toDate)) {
				$orders = $orders->where('orders.order_date', '>=', $fromDate)
					->where('orders.order_date', '<=', $toDate);
			} else if (!empty($fromDate) && empty($toDate)) {
				$orders = $orders->where('orders.order_date', $fromDate);
			} else if (empty($fromDate) && !empty($toDate)) {
				$orders = $orders->where('orders.order_date', '<=', $toDate);
			}

			$stores = DB::table('store')->where('city', $city)->get();
			$orders = $orders->groupby('cart_id')->orderby('orders.order_date', 'ASC')->get();
		} else {
			$orders = array();
		}

		$allowedCities = explode(',',$admin->locations);
        $cities = DB::table('city')->whereIn('city_name',$allowedCities)->get();


		$orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->count();
		$completed_orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->where('order_status', 'Completed')->count();
		$pending_orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->where('order_status', 'Pending')->count();
		$cancelled_orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->where('order_status', 'Cancelled')->count();

		$admin_earning = DB::table('store_orders')
			->leftJoin('orders', 'store_orders.order_cart_id', '=', 'orders.cart_id')
			->leftJoin('store_products', function ($join) {
				$join->on('store_orders.varient_id', '=', 'store_products.varient_id')
					->select('store_id');
			})
			->leftJoin('store', function ($join) {
				$join->on('store_products.store_id', '=', 'store.store_id')
					->select('admin_share');
			})
			->where('orders.order_status', 'Completed')
			->groupby('store_orders.store_order_id')
			->select(DB::raw('((store_orders.price/100)*store.admin_share) as earning'))
			->get()->sum('earning');
		$order_total = DB::table('orders')
			->where('orders.order_status', 'Completed')
			->sum('total_price');

		// $orders=DB::table('orders')
		// ->where('orders.order_status','Completed')
		// ->get();

		$report = array(
			'orders_count' => $orders_count,
			'completed_orders_count' => $completed_orders_count,
			'pending_orders_count' => $pending_orders_count,
			'cancelled_orders_count' => $cancelled_orders_count,
			'orders' => $orders,
			'order_total' => $order_total,
			'admin_earning' => $admin_earning
		);

		$adminTopApp =  DB::table('categories')->get();


		return view('admin.report.report_by_vindoe_list', compact('title', 'admin', 'logo', 'report', 'cities', 'request', 'stores', 'adminTopApp'));
	}


	public function reportByDeliveryAgent(Request $request)
	{
		$title = "Report Delivery Agent";
		$admin_email = Session::get('bamaAdmin');
		$admin = DB::table('admin')
			->where('admin_email', $admin_email)
			->first();
		if (!Helper::hasRight($admin->id, 'reports', 'View') && !Helper::hasRight($admin->id, 'report_by_delivery_agent', 'View')) {
			return abort(403);
		}
		$logo = DB::table('tbl_web_setting')
			->where('set_id', '1')
			->first();
		// $stores = DB::table('store')->get();
		// $delivery_boys = DB::table('delivery_boy')->get();
		DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
		$city = $request->city;
		$paymentModes = $request->payment_modes;
		$dboys_selected = $request->delivery_agents;
		$fromDate = $request->fromdate;
		$toDate = $request->todate;
		$orders = DB::table('orders')->join('store', 'store.store_id', 'orders.store_id')
			->leftJoin('users', function ($join) {
				$join->on('orders.user_id', 'users.user_id')
					->select('user_name');
			})->leftJoin('delivery_boy', 'delivery_boy.dboy_id', 'orders.dboy_id')
			->leftJoin('store_orders as b', function ($join) {
				$join->on('b.order_cart_id', '=', 'orders.cart_id')
					->groupby('order_cart_id');
			})
			->where('orders.payment_method', '!=', NULL)
			->select('*', DB::raw('((orders.price_without_delivery/100)*store.admin_share) as commission'), DB::raw('SUM(qty) as qtysum'));

		$dboys = array();

		if (!empty($city)) {
			$orders = $orders->where('store.city', $city);
			if (!empty($paymentMode)) {
				$orders = $orders->whereIn('orders.payment_method', $paymentModes);
			}
			if (!empty($dboys_selected)) {
				$orders = $orders->whereIn('orders.dboy_id', $dboys_selected);
			}

			if (!empty($fromDate) && !empty($toDate)) {
				$orders = $orders->where('orders.order_date', '>=', $fromDate)
					->where('orders.order_date', '<=', $toDate);
			} else if (!empty($fromDate) && empty($toDate)) {
				$orders = $orders->where('orders.order_date', $fromDate);
			} else if (empty($fromDate) && !empty($toDate)) {
				$orders = $orders->where('orders.order_date', '<=', $toDate);
			}

			$dboys = DB::table('delivery_boy')->where('boy_city', $city)->get();
			$orders = $orders->groupby('cart_id')->orderby('orders.order_date', 'ASC')->get();
		} else {
			$orders = array();
		}

		$allowedCities = explode(',',$admin->locations);
        $cities = DB::table('city')->whereIn('city_name',$allowedCities)->get();


		$orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->count();
		$completed_orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->where('order_status', 'Completed')->count();
		$pending_orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->where('order_status', 'Pending')->count();
		$cancelled_orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->where('order_status', 'Cancelled')->count();

		$admin_earning = DB::table('store_orders')
			->leftJoin('orders', 'store_orders.order_cart_id', '=', 'orders.cart_id')
			->leftJoin('store_products', function ($join) {
				$join->on('store_orders.varient_id', '=', 'store_products.varient_id')
					->select('store_id');
			})
			->leftJoin('store', function ($join) {
				$join->on('store_products.store_id', '=', 'store.store_id')
					->select('admin_share');
			})
			->where('orders.order_status', 'Completed')
			->groupby('store_orders.store_order_id')
			->select(DB::raw('((store_orders.price/100)*store.admin_share) as earning'))
			->get()->sum('earning');
		$order_total = DB::table('orders')
			->where('orders.order_status', 'Completed')
			->sum('total_price');

		// $orders=DB::table('orders')
		// ->where('orders.order_status','Completed')
		// ->get();

		$report = array(
			'orders_count' => $orders_count,
			'completed_orders_count' => $completed_orders_count,
			'pending_orders_count' => $pending_orders_count,
			'cancelled_orders_count' => $cancelled_orders_count,
			'orders' => $orders,
			'order_total' => $order_total,
			'admin_earning' => $admin_earning
		);


		return view('admin.report.report_by_delivery_agent', compact('title', 'admin', 'logo', 'report', 'cities', 'request', 'dboys'));
	}

	public function reportByOrderStatus(Request $request)
	{
		$title = "Report By Order Status";
		$admin_email = Session::get('bamaAdmin');
		$admin = DB::table('admin')
			->where('admin_email', $admin_email)
			->first();
		if (!Helper::hasRight($admin->id, 'reports', 'View') && !Helper::hasRight($admin->id, 'report_by_order_status', 'View')) {
			return abort(403);
		}
		$logo = DB::table('tbl_web_setting')
			->where('set_id', '1')
			->first();
		// $stores = DB::table('store')->get();
		// $delivery_boys = DB::table('delivery_boy')->get();
		DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
		$city = $request->city;
		$fromDate = $request->fromdate;
		$toDate = $request->todate;
		$orders = DB::table('orders')->join('store', 'store.store_id', 'orders.store_id')
			->leftJoin('users', function ($join) {
				$join->on('orders.user_id', 'users.user_id')
					->select('user_name');
			})->leftJoin('delivery_boy', 'delivery_boy.dboy_id', 'orders.dboy_id')
			->leftJoin('store_orders as b', function ($join) {
				$join->on('b.order_cart_id', '=', 'orders.cart_id')
					->groupby('order_cart_id');
			})
			->where('orders.payment_method', '!=', NULL)
			->select('*', DB::raw('((orders.price_without_delivery/100)*store.admin_share) as commission'), DB::raw('SUM(qty) as qtysum'));

		$dboys = array();
		$stores = array();

		if (!empty($city)) {
			$orders = $orders->where('store.city', $city);
			$orderStatus = $request->order_status;
			$dboys_selected = $request->delivery_agents;
			$stores_selected = $request->stores;
			if (!empty($orderStatus)) {
				$orders = $orders->whereIn('orders.order_status', $orderStatus);
			}

			if (!empty($dboys_selected)) {
				$orders = $orders->whereIn('orders.dboy_id', $dboys_selected);
			}

			if (!empty($stores_selected)) {
				$orders = $orders->whereIn('orders.store_id', $stores_selected);
			}
			if (!empty($fromDate) && !empty($toDate)) {
				$orders = $orders->where('orders.order_date', '>=', $fromDate)
					->where('orders.order_date', '<=', $toDate);
			} else if (!empty($fromDate) && empty($toDate)) {
				$orders = $orders->where('orders.order_date', $fromDate);
			} else if (empty($fromDate) && !empty($toDate)) {
				$orders = $orders->where('orders.order_date', '<=', $toDate);
			}

			$stores = DB::table('store')->where('city', $city)->get();

			$dboys = DB::table('delivery_boy')->where('boy_city', $city)->get();
			$orders = $orders->groupby('cart_id')->orderby('orders.order_date', 'ASC')->get();
		} else {
			$orders = array();
		}

		$allowedCities = explode(',',$admin->locations);
        $cities = DB::table('city')->whereIn('city_name',$allowedCities)->get();


		$orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->count();
		$completed_orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->where('order_status', 'Completed')->count();
		$pending_orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->where('order_status', 'Pending')->count();
		$cancelled_orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->where('order_status', 'Cancelled')->count();

		$admin_earning = DB::table('store_orders')
			->leftJoin('orders', 'store_orders.order_cart_id', '=', 'orders.cart_id')
			->leftJoin('store_products', function ($join) {
				$join->on('store_orders.varient_id', '=', 'store_products.varient_id')
					->select('store_id');
			})
			->leftJoin('store', function ($join) {
				$join->on('store_products.store_id', '=', 'store.store_id')
					->select('admin_share');
			})
			->where('orders.order_status', 'Completed')
			->groupby('store_orders.store_order_id')
			->select(DB::raw('((store_orders.price/100)*store.admin_share) as earning'))
			->get()->sum('earning');
		$order_total = DB::table('orders')
			->where('orders.order_status', 'Completed')
			->sum('total_price');

		// $orders=DB::table('orders')
		// ->where('orders.order_status','Completed')
		// ->get();

		$report = array(
			'orders_count' => $orders_count,
			'completed_orders_count' => $completed_orders_count,
			'pending_orders_count' => $pending_orders_count,
			'cancelled_orders_count' => $cancelled_orders_count,
			'orders' => $orders,
			'order_total' => $order_total,
			'admin_earning' => $admin_earning
		);


		return view('admin.report.report_by_order_status', compact('title', 'admin', 'logo', 'report', 'cities', 'request', 'dboys', 'stores'));
	}

	public function reportByVendorOrderStatus(Request $request)
	{
		$title = "Report By Vendor Order Status";
		$admin_email = Session::get('bamaAdmin');
		$admin = DB::table('admin')
			->where('admin_email', $admin_email)
			->first();
		if (!Helper::hasRight($admin->id, 'reports', 'View') && !Helper::hasRight($admin->id, 'report_by_vendor_order_status', 'View')) {
			return abort(403);
		}
		$logo = DB::table('tbl_web_setting')
			->where('set_id', '1')
			->first();
		// $stores = DB::table('store')->get();
		// $delivery_boys = DB::table('delivery_boy')->get();
		DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
		$city = $request->city;
		$stores_selected = $request->stores;
		$fromDate = $request->fromdate;
		$toDate = $request->todate;
		$orders = DB::table('orders')->join('store', 'store.store_id', 'orders.store_id')
			->leftJoin('users', function ($join) {
				$join->on('orders.user_id', 'users.user_id')
					->select('user_name');
			})->leftJoin('delivery_boy', 'delivery_boy.dboy_id', 'orders.dboy_id')
			->leftJoin('store_orders as b', function ($join) {
				$join->on('b.order_cart_id', '=', 'orders.cart_id')
					->groupby('order_cart_id');
			})
			->where('orders.payment_method', '!=', NULL)
			->select('*', DB::raw('((orders.price_without_delivery/100)*store.admin_share) as commission'), DB::raw('SUM(qty) as qtysum'));

		$stores = array();

		if (!empty($city)) {
			$orders = $orders->where('store.city', $city);
			$orderStatus = $request->order_status;

			if (!empty($orderStatus)) {
				$orders = $orders->whereIn('orders.order_status', $orderStatus);
			}
			if (!empty($stores_selected)) {
				$orders = $orders->whereIn('orders.store_id', $stores_selected);
			}

			if (!empty($fromDate) && !empty($toDate)) {
				$orders = $orders->where('orders.order_date', '>=', $fromDate)
					->where('orders.order_date', '<=', $toDate);
			} else if (!empty($fromDate) && empty($toDate)) {
				$orders = $orders->where('orders.order_date', $fromDate);
			} else if (empty($fromDate) && !empty($toDate)) {
				$orders = $orders->where('orders.order_date', '<=', $toDate);
			}

			$stores = DB::table('store')->where('city', $city)->get();
			$orders = $orders->groupby('cart_id')->orderby('orders.order_date', 'ASC')->get();
		} else {
			$orders = array();
		}


		$allowedCities = explode(',',$admin->locations);
        $cities = DB::table('city')->whereIn('city_name',$allowedCities)->get();


		$orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->count();
		$completed_orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->where('order_status', 'Completed')->count();
		$pending_orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->where('order_status', 'Pending')->count();
		$cancelled_orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->where('order_status', 'Cancelled')->count();

		$admin_earning = DB::table('store_orders')
			->leftJoin('orders', 'store_orders.order_cart_id', '=', 'orders.cart_id')
			->leftJoin('store_products', function ($join) {
				$join->on('store_orders.varient_id', '=', 'store_products.varient_id')
					->select('store_id');
			})
			->leftJoin('store', function ($join) {
				$join->on('store_products.store_id', '=', 'store.store_id')
					->select('admin_share');
			})
			->where('orders.order_status', 'Completed')
			->groupby('store_orders.store_order_id')
			->select(DB::raw('((store_orders.price/100)*store.admin_share) as earning'))
			->get()->sum('earning');
		$order_total = DB::table('orders')
			->where('orders.order_status', 'Completed')
			->sum('total_price');

		// $orders=DB::table('orders')
		// ->where('orders.order_status','Completed')
		// ->get();

		$report = array(
			'orders_count' => $orders_count,
			'completed_orders_count' => $completed_orders_count,
			'pending_orders_count' => $pending_orders_count,
			'cancelled_orders_count' => $cancelled_orders_count,
			'orders' => $orders,
			'order_total' => $order_total,
			'admin_earning' => $admin_earning
		);


		return view('admin.report.report_by_vendor_order_status', compact('title', 'admin', 'logo', 'report', 'cities', 'request', 'stores'));
	}

	public function reportByDeliveryAgentOrderStatus(Request $request)
	{
		$title = "Report By Delivery Agent Order Status";
		$admin_email = Session::get('bamaAdmin');
		$admin = DB::table('admin')
			->where('admin_email', $admin_email)
			->first();
		if (!Helper::hasRight($admin->id, 'reports', 'View') && !Helper::hasRight($admin->id, 'report_by_dboy_order_status', 'View')) {
			return abort(403);
		}
		$logo = DB::table('tbl_web_setting')
			->where('set_id', '1')
			->first();
		// $stores = DB::table('store')->get();
		// $delivery_boys = DB::table('delivery_boy')->get();
		DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
		$city = $request->city;
		$dboys_selected = $request->delivery_agents;
		$fromDate = $request->fromdate;
		$toDate = $request->todate;
		$orders = DB::table('orders')->join('store', 'store.store_id', 'orders.store_id')
			->leftJoin('users', function ($join) {
				$join->on('orders.user_id', 'users.user_id')
					->select('user_name');
			})->leftJoin('delivery_boy', 'delivery_boy.dboy_id', 'orders.dboy_id')
			->leftJoin('store_orders as b', function ($join) {
				$join->on('b.order_cart_id', '=', 'orders.cart_id')
					->groupby('order_cart_id');
			})
			->where('orders.payment_method', '!=', NULL)
			->select('*', DB::raw('((orders.price_without_delivery/100)*store.admin_share) as commission'), DB::raw('SUM(qty) as qtysum'));

		$dboys = array();

		if (!empty($city)) {
			$orders = $orders->where('store.city', $city);
			$orderStatus = $request->order_status;
			if (!empty($orderStatus)) {
				$orders = $orders->whereIn('orders.order_status', $orderStatus);
			}
			if (!empty($dboys_selected)) {
				$orders = $orders->whereIn('orders.dboy_id', $dboys_selected);
			}

			if (!empty($fromDate) && !empty($toDate)) {
				$orders = $orders->where('orders.order_date', '>=', $fromDate)
					->where('orders.order_date', '<=', $toDate);
			} else if (!empty($fromDate) && empty($toDate)) {
				$orders = $orders->where('orders.order_date', $fromDate);
			} else if (empty($fromDate) && !empty($toDate)) {
				$orders = $orders->where('orders.order_date', '<=', $toDate);
			}

			$dboys = DB::table('delivery_boy')->where('boy_city', $city)->get();
			$orders = $orders->groupby('cart_id')->orderby('orders.order_date', 'ASC')->get();
		} else {
			$orders = array();
		}

		$allowedCities = explode(',',$admin->locations);
        $cities = DB::table('city')->whereIn('city_name',$allowedCities)->get();


		$orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->count();
		$completed_orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->where('order_status', 'Completed')->count();
		$pending_orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->where('order_status', 'Pending')->count();
		$cancelled_orders_count = DB::table('orders')
			->where('order_date', date('Y-m-d H:i:s'))
			->where('order_status', 'Cancelled')->count();

		$admin_earning = DB::table('store_orders')
			->leftJoin('orders', 'store_orders.order_cart_id', '=', 'orders.cart_id')
			->leftJoin('store_products', function ($join) {
				$join->on('store_orders.varient_id', '=', 'store_products.varient_id')
					->select('store_id');
			})
			->leftJoin('store', function ($join) {
				$join->on('store_products.store_id', '=', 'store.store_id')
					->select('admin_share');
			})
			->where('orders.order_status', 'Completed')
			->groupby('store_orders.store_order_id')
			->select(DB::raw('((store_orders.price/100)*store.admin_share) as earning'))
			->get()->sum('earning');
		$order_total = DB::table('orders')
			->where('orders.order_status', 'Completed')
			->sum('total_price');

		// $orders=DB::table('orders')
		// ->where('orders.order_status','Completed')
		// ->get();

		$report = array(
			'orders_count' => $orders_count,
			'completed_orders_count' => $completed_orders_count,
			'pending_orders_count' => $pending_orders_count,
			'cancelled_orders_count' => $cancelled_orders_count,
			'orders' => $orders,
			'order_total' => $order_total,
			'admin_earning' => $admin_earning
		);


		return view('admin.report.report_by_delivery_agent_order_status', compact('title', 'admin', 'logo', 'report', 'cities', 'request', 'dboys'));
	}


	public function getreport(Request $request)
	{
		$admin_email = Session::get('bamaAdmin');
		$admin = DB::table('admin')
			->where('admin_email', $admin_email)
			->first();
		if (!Helper::hasRight($admin->id, 'reports', 'View') && !Helper::hasRight($admin->id, 'reports', 'View')) {
			return abort(403);
		}
		$city = $request->city;
		$stores = $request->stores;
		$dboys = $request->delivery_boy;
		$fromDate = $request->fromdate;
		$toDate = $request->todate;
		$orders = DB::table('orders')->where('orders.payment_method', '!=', NULL);


		if (empty($stores) && empty($dboys)) {
			$orders = $orders->join('store', 'store.store_id', 'orders.store_id')
				->where('store.city', $city);
		} else if (empty($dboys)) {
			$orders = $orders->join('store', 'store.store_id', 'orders.store_id')
				->whereIn('orders.store_id', $stores);
		} else {
			$orders = $orders->join('store', 'store.store_id', 'orders.store_id')
				->whereIn('orders.store_id', $stores)
				->whereIn('orders.dboy_id', $dboys);
		}

		if (!empty($fromDate) && !empty($toDate)) {
			$orders = $orders->where('order_date', '>=', $fromDate)
				->where('order_date', '<=', $toDate);
		}

		$orders = $orders->get();


		return array('orders' => $orders);
	}

	public function get_stores_dboy_by_city(Request $request)
	{
		$admin_email = Session::get('bamaAdmin');
		$admin = DB::table('admin')
			->where('admin_email', $admin_email)
			->first();
		if (!Helper::hasRight($admin->id, 'reports', 'View') && !Helper::hasRight($admin->id, 'stores', 'View') && !Helper::hasRight($admin->id, 'reports', 'View') && !Helper::hasRight($admin->id, 'delivery_agent', 'View')) {
			return abort(403);
		}
		$stores = DB::table('store')
			->where('city', $request->city)
			->get();
		$dboys = DB::table('delivery_boy')->where('boy_city', $request->city)->get();
		return array('stores' => $stores, 'dboys' => $dboys);
	}
}
