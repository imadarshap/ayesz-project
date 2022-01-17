<style>
  .activate {
    background-color: #b5b5c0 !important;
    color: white !important;
  }
</style>
<div class="sidebar" data-color="purple" data-background-color="white" data-image="../assets/img/sidebar-1.jpg">
  <div class="logo"><a href="{{route('adminHome')}}" class="simple-text logo-normal">
      {{$logo->name}}
    </a></div>
  <div class="sidebar-wrapper" id="menu">
    <ul class="nav">
      @if(Helper::hasRight($admin->id,'dashboard','View'))
      <li class="{{ (request()->is('home')) ? 'active' : '' }} nav-item">
        <a class="nav-link" href="{{route('adminHome')}}" active>
          <i class="material-icons">dashboard</i>
          <p>Dashboard</p>
        </a>
      </li>
      @endif
      @if(Helper::hasRight($admin->id,'orders','View'))
      <li class="{{ (strpos(request()->url(), 'admin/orders/') !== false) ? 'active' : '' }} nav-item">
        <a class="nav-link" href="{{route('allOrders','all')}}" active>
          <i class="material-icons">layers</i>
          <p>Orders</p>
        </a>
      </li>
      @endif


      <!-- <li class="nav-item {{ (request()->is('admin/store/cancelledorders')) ? 'active' : '' }} {{ (request()->is('admin/completed_orders')) ? 'active' : '' }} {{ (request()->is('admin/pending_orders')) ? 'active' : '' }} {{ (request()->is('admin/cancelled_orders')) ? 'active' : '' }}">
            <a class="nav-link" data-toggle="collapse" href="#ord-dropdown" aria-expanded="false" aria-controls="setting-dropdown">
             <i class="material-icons">layers</i>
              <span class="menu-title">Orders<b class="caret"></b></span>
            </a>
            <div class="collapse {{ (request()->is('admin/store/cancelledorders')) ? 'show' : '' }} {{ (request()->is('admin/completed_orders')) ? 'show' : '' }} {{ (request()->is('admin/pending_orders')) ? 'show' : '' }} {{ (request()->is('admin/cancelled_orders')) ? 'show' : '' }}" id="ord-dropdown">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                  <a class="nav-link {{ (request()->is('admin/store/cancelledorders')) ? 'activate' : '' }}" href="{{route('store_cancelled')}}">Rejected By Store</a>
                </li>
                <li class="nav-item">
                  <a class="{{ (request()->is('admin/completed_orders')) ? 'activate' : '' }} nav-link" href="{{route('admin_com_orders')}}">Completed Orders</a>
                </li>
                  <li class="nav-item">
                  <a class="nav-link {{ (request()->is('admin/pending_orders')) ? 'activate' : '' }}" href="{{route('admin_pen_orders')}}">Pending Orders</a>
                </li>
                  <li class="nav-item">
                  <a class="{{ (request()->is('admin/cancelled_orders')) ? 'activate' : '' }} nav-link" href="{{route('admin_can_orders')}}">Cancelled Orders</a>
                </li>
                </ul>
                </div>
          </li> -->
      @if(Helper::hasRight($admin->id,'categories','View') || Helper::hasRight($admin->id,'products','View'))
      <li class="nav-item {{ (request()->is('category/list')) ? 'active' : '' }} {{ (request()->is('product/list')) ? 'active' : '' }} {{ (request()->is('deal/list')) ? 'active' : '' }} {{ (request()->is('top-cat')) ? 'active' : '' }}{{ (request()->is('bulk/upload')) ? 'active' : '' }}">
        <a class="nav-link" data-toggle="collapse" href="#cat-dropdown" aria-expanded="false" aria-controls="setting-dropdown">
          <i class="material-icons">content_paste</i>
          <span class="menu-title">Category/products<b class="caret"></b></span>
        </a>
        <div class="collapse {{ (request()->is('category/list')) ? 'show' : '' }} {{ (request()->is('product/list')) ? 'show' : '' }} {{ (request()->is('deal/list')) ? 'show' : '' }} {{ (request()->is('top-cat')) ? 'show' : '' }}{{ (request()->is('bulk/upload')) ? 'show' : '' }}" id="cat-dropdown">
          <ul class="nav flex-column sub-menu">
            @if(Helper::hasRight($admin->id,'categories','View'))
            <li class="nav-item">
              <a class="{{ (request()->is('category/list')) ? 'activate' : '' }} nav-link" href="{{route('catlist')}}">Categories</a>
            </li>
            @endif
            @if(Helper::hasRight($admin->id,'products','View'))
            <li class="nav-item">
              <a class="{{ (request()->is('product/list')) ? 'activate' : '' }} nav-link" href="{{route('productlist')}}">Product</a>
            </li>
            <li class="nav-item">
              <a class="{{ (request()->is('bulk/upload')) ? 'activate' : '' }} nav-link" href="{{route('bulkup')}}">Bulk Upload</a>
            </li>
            <li class="nav-item">
              <a class="{{ (request()->is('deal/list')) ? 'activate' : '' }} nav-link" href="{{route('deallist')}}">Deal Products</a>
            </li>
            @endif
            @if(Helper::hasRight($admin->id,'categories','View'))
            <li class="nav-item">
              <a class="{{ (request()->is('top-cat')) ? 'activate' : '' }} nav-link" href="{{route('adminTopCat')}}">Home Category</a>
            </li>
            @endif
          </ul>
        </div>
      </li>
      @endif
      @if(Helper::hasRight($admin->id,'vendors','View') || Helper::hasRight($admin->id,'vendor_earnings','View') || Helper::hasRight($admin->id,'vendor_approval_list','View'))
      <li class="nav-item {{ (request()->is('admin/store/list')) ? 'active' : '' }} {{ (request()->is('finance')) ? 'active' : '' }} {{ (request()->is('waiting_for_approval/stores/list')) ? 'active' : '' }}">
        <a class="nav-link" data-toggle="collapse" href="#store-dropdown" aria-expanded="false" aria-controls="setting-dropdown">
          <i class="material-icons">house</i>
          <span class="menu-title">Vendor Management<b class="caret"></b></span>
        </a>
        <div class="collapse {{ (request()->is('admin/store/list')) ? 'show' : '' }} {{ (request()->is('finance')) ? 'show' : '' }} {{ (request()->is('waiting_for_approval/stores/list')) ? 'show' : '' }}" id="store-dropdown">
          <ul class="nav flex-column sub-menu">
            @if(Helper::hasRight($admin->id,'vendors','View'))
            <li class="nav-item">
              <a class="nav-link {{ (request()->is('admin/store/list')) ? 'activate' : '' }}" href="{{route('storeclist')}}">Vendor</a>
            </li>
            @endif
            @if(Helper::hasRight($admin->id,'vendor_earnings','View'))
            <li class="nav-item">
              <a class="nav-link {{ (request()->is('finance')) ? 'activate' : '' }}" href="{{route('finance')}}">Vendor Earnings/Payments</a>
            </li>
            @endif
            @if(Helper::hasRight($admin->id,'vendor_approval_list','View'))
            <li class="nav-item">
              <a class="nav-link {{ (request()->is('waiting_for_approval/stores/list')) ? 'activate' : '' }}" href="{{route('storeapprove')}}">Waiting For Approval</a>
            </li>
            @endif
          </ul>
        </div>
      </li>
      @endif
      @if(Helper::hasRight($admin->id,'vendor_priority','View'))
      <li class="nav-item {{ (request()->is('store_priority')) ? 'active' : '' }}">
        <a class="nav-link" href="{{route('store_priority')}}">
          <i class="material-icons">list</i>
          <p>Vendor Priority</p>
        </a>
      </li>
      @endif
      @if(Helper::hasRight($admin->id,'customers','View'))
      <li class="nav-item {{ (request()->is('user/list')) ? 'active' : '' }}">
        <a class="nav-link" href="{{route('userlist')}}">
          <i class="material-icons">android</i>
          <p>Customers</p>
        </a>
      </li>
      @endif
      @if(Helper::hasRight($admin->id,'delivery_agent','View'))
      <li class="nav-item {{ (request()->is('d_boy/list')) ? 'active' : '' }}">
        <a class="nav-link" href="{{route('d_boylist')}}">
          <i class="material-icons">delivery_dining</i>
          <p>Delivery Agent</p>
        </a>
      </li>
      @endif
      @if(Helper::hasRight($admin->id,'reports','View'))
      <li class="nav-item {{ (request()->is('report')) ? 'active' : '' }} {{ (request()->is('report/payment-mode')) ? 'active' : '' }} {{ (request()->is('report/vendor')) ? 'active' : '' }} {{ (request()->is('report/delivery-agent')) ? 'active' : '' }} {{ (request()->is('report/order-status')) ? 'show' : '' }}  {{ (request()->is('report/vendor-order-status')) ? 'active' : '' }}{{ (request()->is('report/delivery-agent-order-status')) ? 'active' : '' }}">
        <a class="nav-link" data-toggle="collapse" href="#report-dropdown" aria-expanded="false" aria-controls="setting-dropdown">
          <i class="material-icons">bar_chart</i>
          <span class="menu-title">Report<b class="caret"></b></span>
        </a>
        <div class="collapse {{ (request()->is('report')) ? 'show' : '' }} {{ (request()->is('report/report_by_vindoe_list')) ? 'show' : '' }} {{ (request()->is('report/report_by_product_list')) ? 'show' : '' }} {{ (request()->is('report/payment-mode')) ? 'show' : '' }} {{ (request()->is('report/vendor')) ? 'show' : '' }} {{ (request()->is('report/delivery-agent')) ? 'show' : '' }} {{ (request()->is('report/order-status')) ? 'show' : '' }}  {{ (request()->is('report/vendor-order-status')) ? 'show' : '' }}{{ (request()->is('report/delivery-agent-order-status')) ? 'show' : '' }}" id="report-dropdown">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item">
              <a class="{{ (request()->is('report/report_by_product_list')) ? 'activate' : '' }} nav-link" href="{{route('report/report_by_product_list')}}">Report By Product List</a>
            </li>
            <li class="nav-item">
              <a class="{{ (request()->is('report/report_by_vindoe_list')) ? 'activate' : '' }} nav-link" href="{{route('report/report_by_vindoe_list')}}">Report By Vendor List</a>
            </li>
            <li class="nav-item">
              <a class="{{ (request()->is('report')) ? 'activate' : '' }} nav-link" href="{{route('report')}}">Vendor Payment Report</a>
            </li>
            <li class="nav-item">
              <a class="{{ (request()->is('report/payment-mode')) ? 'activate' : '' }} nav-link" href="{{route('report/payment-mode')}}">Payment Mode</a>
            </li>
            <li class="nav-item">
              <a class="{{ (request()->is('report/vendor')) ? 'activate' : '' }} nav-link" href="{{route('report/vendor')}}">Vendor Report</a>
            </li>
            <li class="nav-item">
              <a class="{{ (request()->is('report/delivery-agent')) ? 'activate' : '' }} nav-link" href="{{route('report/delivery-agent')}}">Delivery Agent Report</a>
            </li>
            <li class="nav-item">
              <a class="{{ (request()->is('report/order-status')) ? 'activate' : '' }} nav-link" href="{{route('report/order-status')}}">Order Status Report</a>
            </li>
            <li class="nav-item">
              <a class="{{ (request()->is('report/vendor-order-status')) ? 'activate' : '' }} nav-link" href="{{route('report/vendor-order-status')}}">Vendor Order Status</a>
            </li>
            <li class="nav-item">
              <a class="{{ (request()->is('report/delivery-agent-order-status')) ? 'activate' : '' }} nav-link" href="{{route('report/delivery-agent-order-status')}}">Delivery Agent Order Status</a>
            </li>
          </ul>
        </div>
      </li>
      @endif
      @if(Helper::hasRight($admin->id,'cities','View'))
      <li class="nav-item {{ (request()->is('citylist')) ? 'active' : '' }}">
        <a class="nav-link" href="{{route('citylist')}}">
          <i class="material-icons">location_city</i>
          <p>Cities</p>
        </a>
      </li>
      @endif
      @if(Helper::hasRight($admin->id,'payout','View'))
      <li class="nav-item {{ (request()->is('payout_req')) ? 'active' : '' }} {{ (request()->is('prv')) ? 'active' : '' }}">
        <a class="nav-link" data-toggle="collapse" href="#payout-dropdown3" aria-expanded="false" aria-controls="setting-dropdown2">
          <i class="menu-icon fa fa-rupee"></i>
          <span class="menu-title">Payout Request/Validation<b class="caret"></b></span>
        </a>
        <div class="collapse {{ (request()->is('payout_req')) ? 'show' : '' }} {{ (request()->is('prv')) ? 'show' : '' }}" id="payout-dropdown3">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item">
              <a class="{{ (request()->is('payout_req')) ? 'activate' : '' }} nav-link" href="{{route('pay_req')}}">Payout Requests</a>
            </li>
            <li class="nav-item">
              <a class="{{ (request()->is('prv')) ? 'activate' : '' }} nav-link" href="{{route('prv')}}">Payout value validation</a>
            </li>
          </ul>
        </div>
      </li>
      @endif
      @if(Helper::hasRight($admin->id,'delivery_charges','View'))
      <li class="nav-item ">
        <a class="nav-link" href="{{route('orderedit')}}">
          <i class="material-icons">bubble_chart</i>
          <p>Delivery Charges</p>
        </a>
      </li>
      @endif
      @if(Helper::hasRight($admin->id,'notifications','View'))
      <li class="{{ (request()->is('Notification')) ? 'active' : '' }} nav-item">
        <a class="nav-link" href="{{route('adminNotification')}}">
          <i class="material-icons">notifications</i>
          <span class="menu-title">Send Notification</span>
        </a>
      </li>
      @endif
      @if(Helper::hasRight($admin->id,'admin_users','View'))
      <li class="{{ (request()->is('admin_users')) ? 'active' : '' }} nav-item">
        <a class="nav-link" href="{{route('admin_users.index')}}">
          <i class="material-icons">group</i>
          <span class="menu-title">Admin Management</span>
        </a>
      </li>
      @endif
      @if(Helper::hasRight($admin->id,'settings','View'))
      <li class="nav-item {{ (request()->is('global_settings')) ? 'active' : '' }} {{ (request()->is('app_settings')) ? 'active' : '' }} {{ (request()->is('msgby')) ? 'active' : '' }} {{ (request()->is('map_api')) ? 'active' : '' }} {{ (request()->is('app_notice')) ? 'active' : '' }}">
        <a class="nav-link" data-toggle="collapse" href="#setting-dropdown2" aria-expanded="false" aria-controls="setting-dropdown">
          <i class="material-icons">settings</i>
          <span class="menu-title">Settings<b class="caret"></b></span>
        </a>
        <div class="collapse  {{ (request()->is('global_settings')) ? 'show' : '' }} {{ (request()->is('app_settings')) ? 'show' : '' }} {{ (request()->is('msgby')) ? 'show' : '' }} {{ (request()->is('map_api')) ? 'show' : '' }}{{ (request()->is('app_notice')) ? 'show' : '' }}" id="setting-dropdown2">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item">
              <a class="{{ (request()->is('global_settings')) ? 'activate' : '' }} nav-link" href="{{route('app_details')}}">Global Settings</a>
            </li>
            <li class="nav-item">
              <a class="{{ (request()->is('app_settings')) ? 'activate' : '' }} nav-link" href="{{route('app_settings')}}"> App Settings</a>
            </li>
            <li class="nav-item">
              <a class="{{ (request()->is('msgby')) ? 'activate' : '' }} nav-link" href="{{route('msg91')}}">SMS/OTP API</a>
            </li>
            <li class="nav-item">
              <a class="{{ (request()->is('map_api')) ? 'activate' : '' }} nav-link" href="{{route('mapapi')}}">Map API</a>
            </li>
            <li class="nav-item">
              <a class="{{ (request()->is('app_notice')) ? 'activate' : '' }} nav-link" href="{{route('app_notice')}}">App Notice</a>
            </li>
          </ul>
        </div>
      </li>
      @endif
      @if(Helper::hasRight($admin->id,'reward','View'))
      <li class="nav-item {{ (request()->is('RewardList')) ? 'active' : '' }} {{ (request()->is('reedem')) ? 'active' : '' }}">
        <a class="nav-link" data-toggle="collapse" href="#setting-dropdown3" aria-expanded="false" aria-controls="setting-dropdown2">
          <i class="menu-icon fa fa-trophy"></i>
          <span class="menu-title">Reward<b class="caret"></b></span>
        </a>
        <div class="collapse {{ (request()->is('RewardList')) ? 'show' : '' }} {{ (request()->is('reedem')) ? 'show' : '' }}" id="setting-dropdown3">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item">
              <a class="nav-link {{ (request()->is('RewardList')) ? 'activate' : '' }}" href="{{route('RewardList')}}">Reward Value</a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ (request()->is('reedem')) ? 'activate' : '' }}" href="{{route('reedem')}}">Reedem Value</a>
            </li>
          </ul>
        </div>
      </li>
      @endif
      @if(Helper::hasRight($admin->id,'banners','View'))
      <li class="nav-item {{ (request()->is('bannerlist')) ? 'active' : '' }} { (request()->is('secbannerlist')) ? 'active' : '' }}">
        <a class="nav-link" data-toggle="collapse" href="#banner-dropdown" aria-expanded="false" aria-controls="setting-dropdown">
          <i class="material-icons">image</i>
          <span class="menu-title">Banner<b class="caret"></b></span>
        </a>
        <div class="collapse {{ (request()->is('bannerlist')) ? 'show' : '' }} {{ (request()->is('secbannerlist')) ? 'show' : '' }}" id="banner-dropdown">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item">
              <a class="nav-link {{ (request()->is('bannerlist')) ? 'activate' : '' }}" href="{{route('bannerlist')}}">Main Banner</a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ (request()->is('secbannerlist')) ? 'activate' : '' }}" href="{{route('secbannerlist')}}">Secondary Banner</a>
            </li>
          </ul>
        </div>
      </li>
      @endif
      @if(Helper::hasRight($admin->id,'coupons','View'))
      <li class="nav-item {{ (request()->is('couponlist')) ? 'active' : '' }}">
        <a class="nav-link" href="{{route('couponlist')}}">
          <i class="material-icons">view_week</i>
          <p>Coupon</p>
        </a>
      </li>
      @endif
      @if(Helper::hasRight($admin->id,'vendors','Edit'))
      <li class="nav-item {{ (request()->is('about_us')) ? 'active' : '' }} {{ (request()->is('terms')) ? 'active' : '' }}">
        <a class="nav-link" data-toggle="collapse" href="#pages-dropdown" aria-expanded="false" aria-controls="setting-dropdown">
          <i class="menu-icon fa fa-calendar"></i>
          <span class="menu-title">Pages<b class="caret"></b></span>
        </a>
        <div class="collapse {{ (request()->is('about_us')) ? 'show' : '' }} {{ (request()->is('terms')) ? 'show' : '' }}" id="pages-dropdown">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item">
              <a class="nav-link {{ (request()->is('about_us')) ? 'activate' : '' }}" href="{{route('about_us')}}">About Us</a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ (request()->is('terms')) ? 'activate' : '' }}" href="{{route('terms')}}">Terms & Condition</a>
            </li>
          </ul>
        </div>
      </li>
      @endif
    </ul>
  </div>
</div>
<script>
  // Add active class to the current button (highlight it)
  var header = document.getElementById("menu");
  var btns = header.getElementsByClassName("nav-item");
  for (var i = 0; i < btns.length; i++) {
    btns[i].addEventListener("click", function() {
      var current = document.getElementsByClassName("active");
      current[0].className = current[0].className.replace(" active", "");
      this.className += " active";
    });
  }
</script>