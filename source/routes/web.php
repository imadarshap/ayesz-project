<?php

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/clear-cache', function() {
//     Artisan::call('config:cache');
//     return "Cache is cleared";
// });


Route::group(['prefix' => 'web', ['middleware' => ['XSS']], 'namespace' => 'Web'], function () {
	// for login
	Route::get('/', 'UserloginController@userlogin')->name('userLogin');
	Route::post('custloginCheck', 'UserloginController@logincheck')->name('custLoginCheck');

	Route::get('sign-up', 'RegisterController@register_user')->name('userregister');
	Route::post('registration', 'RegisterController@usersignup')->name('user_registration');
	Route::post('registration/otp_verify', 'RegisterController@web_verify_otp')->name('web_verify_otp');

	Route::group(['middleware' => 'bamaCust'], function () {

		Route::get('about', 'WebHomeController@aboutus')->name('webabout');
		Route::get('terms', 'WebHomeController@terms')->name('terms');
		Route::get('home/', 'WebHomeController@web')->name('webhome');

		Route::get('products', 'AllProductController@products')->name('products');
		Route::get('products/{cat_id}', 'AllProductController@cate')->name('catee');
		Route::get('user/logout', 'UserloginController@logout')->name('userlogout');
	});
});

Route::group(['prefix' => '', ['middleware' => ['XSS']], 'namespace' => 'Admin'], function () {
	// for login
	// Route::get('/', 'LoginController@adminLogin')->name('adminLogin');
	Route::get('/', 'LoginController@home')->name('home');
	Route::get('about-us', 'LoginController@about')->name('about-us');
	Route::get('privacy-policy', 'LoginController@privacyPolicy')->name('privacy-policy');
	Route::get('privacy', 'LoginController@privacy')->name('privacy');
	Route::get('terms-conditions', 'LoginController@termsConditions')->name('terms-conditions');
	Route::get('shipping-policy', 'LoginController@shippingPolicy')->name('shipping-policy');
	Route::get('refund-policy', 'LoginController@refundPolicy')->name('refund-policy');
	Route::get('return-policy', 'LoginController@returnPolicy')->name('return-policy');
	Route::get('career-opportunities', 'LoginController@careerOpportunities')->name('career-opportunities');
	Route::get('admin-login', 'LoginController@adminLogin')->name('adminLogin');
	Route::post('loginCheck', 'LoginController@adminLoginCheck')->name('adminLoginCheck');

	Route::group(['middleware' => 'bamaAdmin'], function () {
		Route::get('home', 'HomeController@adminHome')->name('adminHome');
		Route::get('logout', 'LoginController@logout')->name('logout');
		Route::get('profile', 'ProfileController@adminProfile')->name('prof');
		Route::post('profile/update/{id}', 'ProfileController@adminUpdateProfile')->name('updateprof');
		Route::get('password/change', 'ProfileController@adminChangePass')->name('passchange');
		Route::post('password/update/{id}', 'ProfileController@adminChangePassword')->name('updatepass');

		/////settings/////
		Route::get('global_settings', 'SettingsController@app_details')->name('app_details');
		Route::post('app_details/update', 'SettingsController@updateappdetails')->name('updateappdetails');


		Route::get('msgby', 'SettingsController@msg91')->name('msg91');
		Route::post('msg91/update', 'SettingsController@updatemsg91')->name('updatemsg91');
		Route::post('twilio/update', 'TwilioController@updatetwilio')->name('updatetwilio');
		Route::post('msgoff', 'TwilioController@msgoff')->name('msgoff');

		Route::get('map_api', 'MapController@mapsettings')->name('mapapi');
		Route::post('map_api/update', 'MapController@updategooglemap')->name('updatemap');
		Route::post('mapbox/update', 'MapController@updatemapbox')->name('updatemapbox');

		Route::get('app_settings', 'SettingsController@fcm')->name('app_settings');
		Route::post('app_settings/update', 'SettingsController@updatefcm')->name('updatefcm');

		Route::post('del_charge/update', 'SettingsController@updatedel_charge')->name('updatedel_charge');

		Route::get('Notification', 'NotificationController@adminNotification')->name('adminNotification');
		Route::post('Notification/send', 'NotificationController@adminNotificationSend')->name('adminNotificationSend');

		Route::post('currency/update', 'SettingsController@updatecurrency')->name('updatecurrency');

		Route::get('Notification_to_store', 'NotificationController@Notification_to_store')->name('Notification_to_store');
		Route::post('Notification_to_store/send', 'NotificationController@Notification_to_store_Send')->name('adminNotificationSendtostore');
		///////category////////
		Route::get('category/list', 'CategoryController@list')->name('catlist');
		Route::get('category/add', 'CategoryController@AddCategory')->name('AddCategory');
		Route::post('category/add/new', 'CategoryController@AddNewCategory')->name('AddNewCategory');
		Route::get('category/edit/{category_id}', 'CategoryController@EditCategory')->name('EditCategory');
		Route::post('category/update/{category_id}', 'CategoryController@UpdateCategory')->name('UpdateCategory');
		Route::get('category/delete/{category_id}', 'CategoryController@DeleteCategory')->name('DeleteCategory');
		Route::post('change-postion', 'CategoryController@changePostion')->name('change-postion');

		///////Product////////
		Route::get('product/list', 'ProductController@list')->name('productlist');
		Route::get('product/add', 'ProductController@AddProduct')->name('AddProduct');
		Route::post('product/add/new', 'ProductController@AddNewProduct')->name('AddNewProduct');
		Route::get('product/edit/{product_id}', 'ProductController@EditProduct')->name('EditProduct');
		Route::post('product/update/{product_id}', 'ProductController@UpdateProduct')->name('UpdateProduct');
		Route::get('product/delete/{product_id}', 'ProductController@DeleteProduct')->name('DeleteProduct');
		Route::get('product/get_list/', 'ProductController@getList')->name('getProductList');


		//////Product Varient//////////
		Route::get('varient/{id}', 'VarientController@varient')->name('varient');
		Route::get('varient/add/{id}', 'VarientController@Addproduct')->name('add-varient');
		Route::post('varient/add/new', 'VarientController@AddNewproduct')->name('AddNewvarient');
		Route::get('varient/edit/{id}', 'VarientController@Editproduct')->name('edit-varient');
		Route::post('varient/update/{id}', 'VarientController@Updateproduct')->name('update-varient');
		Route::get('varient/delete/{id}', 'VarientController@deleteproduct')->name('delete-varient');

		///////Delivery Boy////////
		Route::get('d_boy/list', 'DeliveryController@list')->name('d_boylist');
		Route::get('d_boy/add', 'DeliveryController@AddD_boy')->name('AddD_boy');
		Route::post('d_boy/add/new', 'DeliveryController@AddNewD_boy')->name('AddNewD_boy');
		Route::get('d_boy/edit/{id}', 'DeliveryController@EditD_boy')->name('EditD_boy');
		Route::post('d_boy/update/{id}', 'DeliveryController@UpdateD_boy')->name('UpdateD_boy');
		Route::get('d_boy/delete/{id}', 'DeliveryController@DeleteD_boy')->name('DeleteD_boy');

		///////Deal Product////////
		Route::get('deal/list', 'DealController@list')->name('deallist');
		Route::get('deal/add', 'DealController@AddDeal')->name('AddDeal');
		Route::post('deal/add/new', 'DealController@AddNewDeal')->name('AddNewDeal');
		Route::get('deal/edit/{id}', 'DealController@EditDeal')->name('EditDeal');
		Route::post('deal/update/{id}', 'DealController@UpdateDeal')->name('UpdateDeal');
		Route::get('deal/delete/{id}', 'DealController@DeleteDeal')->name('DeleteDeal');


		///////User////////
		Route::get('user/list', 'UserController@list')->name('userlist');
		Route::get('user/block/{id}', 'UserController@block')->name('userblock');
		Route::get('user/unblock/{id}', 'UserController@unblock')->name('userunblock');

		//for Report
		Route::get('report', 'ReportController@report')->name('report');
		Route::get('report/payment-mode', 'ReportController@reportByPaymentMode')->name('report/payment-mode');


		Route::get('report/vendor', 'ReportController@reportByVendor')->name('report/vendor');


		Route::get('report/report_by_product_list', 'ReportController@report_by_product_list')->name('report/report_by_product_list');
		Route::get('report/report_by_vindoe_list', 'ReportController@report_by_vindoe_list')->name('report/report_by_vindoe_list');


		Route::get('report/delivery-agent', 'ReportController@reportByDeliveryAgent')->name('report/delivery-agent');
		Route::get('report/order-status', 'ReportController@reportByOrderStatus')->name('report/order-status');
		Route::get('report/vendor-order-status', 'ReportController@reportByVendorOrderStatus')->name('report/vendor-order-status');
		Route::get('report/delivery-agent-order-status', 'ReportController@reportByDeliveryAgentOrderStatus')->name('report/delivery-agent-order-status');

		Route::post('getreport', 'ReportController@getreport')->name('getreport');
		Route::get('get_stores_dboy_by_city', 'ReportController@get_stores_dboy_by_city')->name('get_stores_dboy_by_city');
		// for city
		Route::get('citylist', 'CityController@citylist')->name('citylist');
		Route::get('city', 'CityController@city')->name('city');
		Route::post('cityadd', 'CityController@cityadd')->name('cityadd');
		Route::get('cityedit/{city_id}', 'CityController@cityedit')->name('cityedit');
		Route::post('cityupdate', 'CityController@cityupdate')->name('cityupdate');
		Route::get('citydelete/{city_id}', 'CityController@citydelete')->name('citydelete');
		// for society
		Route::get('societylist', 'SocietyController@societylist')->name('societylist');
		Route::get('society', 'SocietyController@society')->name('society');
		Route::post('societyadd', 'SocietyController@societyadd')->name('societyadd');
		Route::get('societyedit/{society_id}', 'SocietyController@societyedit')->name('societyedit');
		Route::post('societyupdate', 'SocietyController@societyupdate')->name('societyupdate');
		Route::get('societydelete/{society_id}', 'SocietyController@societydelete')->name('societydelete');
		// for banner
		Route::get('bannerlist', 'BannerController@bannerlist')->name('bannerlist');
		Route::get('banner', 'BannerController@banner')->name('banner');
		Route::post('banneradd', 'BannerController@banneradd')->name('banneradd');
		Route::get('banneredit/{banner_id}', 'BannerController@banneredit')->name('banneredit');
		Route::post('bannerupdate/{banner_id}', 'BannerController@bannerupdate')->name('bannerupdate');
		Route::get('bannerdelete/{society_id}', 'BannerController@bannerdelete')->name('bannerdelete');

		// for coupon
		// 	 
		Route::get('couponlist', 'CouponController@couponlist')->name('couponlist');
		Route::get('coupon', 'CouponController@coupon')->name('coupon');
		Route::post('addcoupon', 'CouponController@addcoupon')->name('addcoupon');
		Route::get('editcoupon/{coupon_id}', 'CouponController@editcoupon')->name('editcoupon');
		Route::post('updatecoupon', 'CouponController@updatecoupon')->name('updatecoupon');
		Route::get('deletecoupon/{coupon_id}', 'CouponController@deletecoupon')->name('deletecoupon');
		// for minimum order
		// 	 Route::get('bannerlist','SocietyController@societylist')->name('societylist');
		//for order value edit
		Route::get('orderedit', 'Minimum_Max_OrderController@orderedit')->name('orderedit');
		Route::post('amountupdate', 'Minimum_Max_OrderController@amountupdate')->name('amountupdate');
		// Route::post('amountupdate','Minimum_Max_OrderController@amountupdate')->name('amountupdate');
		Route::post('amountupdatenew', 'Minimum_Max_OrderController@amountupdatenew')->name('amountupdatenew');
		// for delivery time
		Route::get('timeslot', 'TimeSlotController@timeslot')->name('timeslot');
		Route::post('timeslotupdate', 'TimeSlotController@timeslotupdate')->name('timeslotupdate');
		Route::get('closehour', 'ClosehourController@closehour')->name('closehour');
		Route::post('closehrsupdate', 'ClosehourController@closehrsupdate')->name('closehrsupdate');
		// for store
		Route::get('admin/store/list', 'StoreController@storeclist')->name('storeclist');
		Route::get('admin/store/add', 'StoreController@store')->name('store');
		Route::post('admin/store/added', 'StoreController@storeadd')->name('storeadd');
		Route::get('admin/store/edit/{store_id}', 'StoreController@storedit')->name('storedit');
		Route::post('admin/store/update/{store_id}', 'StoreController@storeupdate')->name('storeupdate');
		Route::get('admin/store/delete/{store_id}', 'StoreController@storedelete')->name('storedelete');
		//store orders//
		//
		//store priority
		Route::get('store_priority', 'StorePriorityController@store_priority')->name('store_priority');
		Route::post('store_priority', 'StorePriorityController@store_priority')->name('store_priority');
		Route::post('set_store_priority', 'StorePriorityController@set_store_priority')->name('set_store_priority');

		Route::get('admin/store/orders/{id}', 'AdminorderController@admin_store_orders')->name('admin_store_orders');

		Route::get('admin/store/cancelledorders', 'AdminorderController@store_cancelled')->name('store_cancelled');


		//assign store//
		Route::post('admin/store/assign/{id}', 'AdminorderController@assignstore')->name('store_assign');

		Route::get('finance', 'FinanceController@finance')->name('finance');
		Route::post('store_pay/{store_id}', 'FinanceController@store_pay')->name('store_pay');


		/////pages////////

		Route::get('about_us', 'PagesController@about_us')->name('about_us');
		Route::post('about_us/update', 'PagesController@updateabout_us')->name('updateabout_us');

		Route::get('terms', 'PagesController@terms')->name('terms');
		Route::post('terms/update', 'PagesController@updateterms')->name('updateterms');

		Route::get('prv', 'SettingsController@prv')->name('prv');
		Route::post('prv/update', 'SettingsController@updateprv')->name('updateprv');

		// for reward
		Route::get('RewardList', 'RewardController@RewardList')->name('RewardList');
		Route::get('reward', 'RewardController@reward')->name('reward');
		Route::post('rewardadd', 'RewardController@rewardadd')->name('rewardadd');
		Route::get('rewardedit/{reward_id}', 'RewardController@rewardedit')->name('rewardedit');
		Route::post('rewardupate', 'RewardController@rewardupate')->name('rewardupate');
		Route::get('rewarddelete/{reward_id}', 'RewardController@rewarddelete')->name('rewarddelete');

		// for reedem
		Route::get('reedem', 'ReedemController@reedem')->name('reedem');
		Route::post('reedemupdate', 'ReedemController@reedemupdate')->name('reedemupdate');

		////store payout////
		Route::get('payout_req', 'PayoutController@pay_req')->name('pay_req');
		Route::post('payout_req/{req_id}', 'PayoutController@store_pay')->name('com_payout');

		// for  Secondary banner
		Route::get('secbannerlist', 'SecondaryBannerController@secbannerlist')->name('secbannerlist');
		Route::get('secbanner', 'SecondaryBannerController@secbanner')->name('secbanner');
		Route::post('secbanneradd', 'SecondaryBannerController@secbanneradd')->name('secbanneradd');
		Route::get('secbanneredit/{sec_banner_id}', 'SecondaryBannerController@secbanneredit')->name('secbanneredit');
		Route::post('secbannerupdate/{sec_banner_id}', 'SecondaryBannerController@secbannerupdate')->name('secbannerupdate');
		Route::get('secbannerdelete/{sec_banner_id}', 'SecondaryBannerController@secbannerdelete')->name('secbannerdelete');

		Route::get('admin/d_boy/orders/{id}', 'AdminorderController@admin_dboy_orders')->name('admin_dboy_orders');
		//assign delivery boy//
		Route::post('admin/d_boy/assign/{id}', 'AdminorderController@assigndboy')->name('dboy_assign');
		////completed orders/////
		Route::get('admin/completed_orders', 'AdminorderController@admin_com_orders')->name('admin_com_orders');
		////Pending orders/////
		Route::get('admin/pending_orders', 'AdminorderController@admin_pen_orders')->name('admin_pen_orders');
		//All Orders
		Route::get('admin/orders', 'AdminorderController@allOrders')->name('allOrders');
		Route::get('admin/get_orders', 'AdminorderController@getOrders')->name('getOrders');
		Route::get('admin/orders/edit/{id}', 'AdminorderController@editOrder')->name('editOrder');
		Route::post('admin/orders/update/{id}', 'AdminorderController@updateOrder')->name('updateOrder');


		Route::get('secretlogin/{id}', 'SecretloginController@secretlogin')->name('secret-login');
		Route::post('admin/reject/order/{id}', 'AdminorderController@rejectorder')->name('admin_reject_order');
		Route::get('admin/cancelled_orders', 'AdminorderController@admin_can_orders')->name('admin_can_orders');
		Route::get('payment_gateway', 'PayController@payment_gateway')->name('gateway');
		Route::post('payment_gateway/update', 'PayController@updatepymntvia')->name('updategateway');


		////approval waiting list////
		Route::get('waiting_for_approval/stores/list', 'ApprovalController@storeclist')->name('storeapprove');
		Route::get('approved/stores/{id}', 'ApprovalController@storeapproved')->name('storeapproved');



		/////////////////////for Top Cat///////////////////////
		Route::get('top-cat', 'TopAppController@adminTopCat')->name('adminTopCat');
		Route::get('top-cat/add', 'TopAppController@adminAddTopCat')->name('adminAddTopCat');
		Route::post('top-cat/add/new', 'TopAppController@adminAddNewTopCat')->name('adminAddNewTopCat');
		Route::get('top-cat/edit/{id}', 'TopAppController@adminEditTopCat')->name('adminEditTopCat');
		Route::post('top-cat/update/{id}', 'TopAppController@adminUpdateTopCat')->name('adminUpdateTopCat');
		Route::get('top-cat/delete/{id}', 'TopAppController@adminTopCatDelete')->name('adminTopCatDelete');

		Route::get('user/delete/{id}', 'UserController@del_user')->name('del_userfromlist');

		Route::get('changeStatus', 'HideController@hideproduct')->name('hideprod');
		Route::get('app_notice', 'NoticeController@adminnotice')->name('app_notice');
		Route::post('app_notice/update', 'NoticeController@adminupdatenotice')->name('app_noticeupdate');
		Route::get('updatefirebase', 'HideController@updatefirebase')->name('updatefirebase');
		/// for bulk upload

		Route::get('bulk/upload', 'ImportExcelController@bulkup')->name('bulkup');
		Route::post('bulk_upload', 'ImportExcelController@import')->name('bulk_upload');
		Route::post('bulk_v_upload', 'ImportExcelController@import_varients')->name('bulk_v_upload');

		//Admin User Rights
		Route::get('admin_users/{id}/status/{status}','AdminController@status')->name('admin_users.status');
		Route::resource('admin_users', 'AdminController');
	});
});

Route::group(['prefix' => 'api', 'namespace' => 'Api'], function () {
	Route::post('forgot_password1/{id}', 'forgotpasswordController@forgot_password1')->name('forgot_password1');
	Route::get('change_pass/{id}', 'forgotpasswordController@change_pass')->name('change_pass');
});


Route::group(['prefix' => 'store', ['middleware' => ['XSS']], 'namespace' => 'Store'], function () {

	// for login
	Route::get('/', 'LoginController@storeLogin')->name('storeLogin');
	Route::get('store_register/', 'StoreregController@register_store')->name('store_register');
	Route::post('store_registered/', 'StoreregController@store_registered')->name('store_registered');
	Route::post('loginCheck', 'LoginController@storeLoginCheck')->name('storeLoginCheck');
	Route::post('vendor_settings', 'LoginController@vendorsettings')->name('vendor_settings');

	Route::group(['middleware' => 'bamaStore'], function () {
		Route::get('home', 'HomeController@storeHome')->name('storeHome');
		Route::get('product/add', 'ProductController@sel_product')->name('sel_product');
		Route::post('product/added', 'ProductController@added_product')->name('added_product');

		Route::get('product/add_multiple_products', 'ProductController@add_multiple_products')->name('add_multiple_products');
		Route::post('product/added_multiple_products', 'ProductController@added_multiple_products')->name('added_multiple_products');

		Route::get('product/delete/{id}', 'ProductController@delete_product')->name('delete_product');
		Route::post('product/stock/{id}', 'ProductController@stock_update')->name('stock_update');
		Route::get('logout', 'LoginController@logout')->name('storelogout');
		Route::get('orders/next_day', 'AssignorderController@orders')->name('storeOrders');
		Route::get('orders/today', 'AssignorderController@assignedorders')->name('storeassignedorders');
		Route::get('orders/all', 'AssignorderController@allOrders')->name('storeAllOrders');
		Route::get('get_orders', 'AssignorderController@getOrders')->name('storeGetOrders');
		Route::get('orders/show/{id}', 'AssignorderController@showOrder')->name('storeShowOrder');

		Route::get('orders/confirm/{cart_id}', 'AssignorderController@confirm_order')->name('store_confirm_order');
		Route::get('orders/reject/{cart_id}', 'AssignorderController@reject_order')->name('store_reject_order');
		Route::get('orders/products/cancel/{store_order_id}', 'OrderController@cancel_products')->name('store_cancel_product');

		Route::get('update/stock', 'ProductController@st_product')->name('st_product');
		Route::get('payout/request', 'PayoutController@payout_req')->name('payout_req');
		Route::post('payout/request/sent', 'PayoutController@req_sent')->name('payout_req_sent');

		/////////invoice
		Route::get('store/invoice/{cart_id}', 'InvoiceController@invoice')->name('invoice');

		/////////invoice
		Route::get('store/pdf/invoice/{cart_id}', 'InvoiceController@pdfinvoice')->name('pdfinvoice');


		Route::get('products/price', 'PriceController@stt_product')->name('stt_product');
		Route::post('product/price/update/{id}', 'PriceController@price_update')->name('price_update');

		Route::get('bulk/upload', 'ImpexcelController@bulkup')->name('bulkuprice');
		Route::post('bulk_upload/price', 'ImpexcelController@import')->name('bulk_uploadprice');
		Route::post('bulk_upload/stock', 'ImpexcelController@importstock')->name('bulk_uploadstock');

		Route::get('settings', 'SettingsController@settings')->name('settings');
		Route::post('set_availability', 'SettingsController@set_availability')->name('set_availability');
		Route::post('change_status', 'SettingsController@change_status')->name('change_status');
	});
});
