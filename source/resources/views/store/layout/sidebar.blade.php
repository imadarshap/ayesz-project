<style>
    .activate{
            background-color: #b5b5c0 !important;
    color: white !important;
    }
</style>
<div class="sidebar" data-color="purple" data-background-color="white" data-image="../assets/img/sidebar-1.jpg">
      <div class="logo"><a href="{{route('storeHome')}}" class="simple-text logo-normal">
          {{$logo->name}} Vendor
        </a></div>
      <div class="sidebar-wrapper">
        <ul class="nav">
            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0)">
                    <i class="material-icons">view</i>
                        <form action="{{route('change_status')}}" method="post" id="statusForm">
                            {{csrf_field()}}
                        <p><input type="checkbox" name="status" class="js-switch" onchange="$('#statusForm').submit()" @if($store->availability==1) checked @endif/>
                        Available</p>
                        </form>
                </a>
          </li>
          <li class="nav-item {{ (request()->is('store/home')) ? 'active' : '' }}">
            <a class="nav-link" href="{{route('storeHome')}}">
              <i class="material-icons">dashboard</i>
              <p>Dashboard</p>
            </a>
          </li>
           <li class="nav-item {{ (request()->is('store/payout/request')) ? 'active' : '' }}">
            <a class="nav-link" href="{{route('payout_req')}}">
              <i class="material-icons">layers</i>
              <p>Send Payout Request</p>
            </a>
          </li>
        
         <li class="nav-item {{ (request()->is('store/orders/today')) ? 'active' : '' }} {{ (request()->is('store/orders/next_day')) ? 'active' : '' }}">
            <a class="nav-link" data-toggle="collapse" href="#setting-dropdown2" aria-expanded="false" aria-controls="setting-dropdown">
              <i class="menu-icon fa fa-calendar"></i>
              <span class="menu-title">Orders<b class="caret"></b></span>
            </a>
            <div class="collapse {{ (request()->is('store/orders/today')) ? 'show' : '' }} {{ (request()->is('store/orders/next_day')) ? 'show' : '' }}" id="setting-dropdown2">
              <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                  <a class="nav-link {{ (request()->is('store/orders/today')) ? 'activate' : '' }}" href="{{route('storeassignedorders')}}">Today Orders</a>
                </li>
 
                <li class="nav-item">
                  <a class="nav-link {{ (request()->is('store/orders/next_day')) ? 'activate' : '' }}" href="{{route('storeOrders')}}">Next-Day Orders</a>
                </li>
               
                </ul>
                </div>
          </li>
          
              <li class="nav-item {{ (request()->is('store/product/add')) ? 'active' : '' }} {{ (request()->is('store/products/price')) ? 'active' : '' }}{{ (request()->is('store/update/stock')) ? 'active' : '' }}{{ (request()->is('store/bulk/upload')) ? 'active' : '' }}{{ (request()->is('store/product/add_multiple_products')) ? 'active' : '' }}">
            <a class="nav-link" data-toggle="collapse" href="#setting-dropdown1" aria-expanded="false" aria-controls="setting-dropdown1">
              <i class="menu-icon fa fa-calendar"></i>
              <span class="menu-title">Products<b class="caret"></b></span>
            </a>
            <div class="collapse {{ (request()->is('store/product/add')) ? 'show' : '' }} {{ (request()->is('store/products/price')) ? 'show' : '' }}{{ (request()->is('store/update/stock')) ? 'show' : '' }}{{ (request()->is('store/bulk/upload')) ? 'show' : '' }} {{ (request()->is('store/product/add_multiple_products')) ? 'show' : '' }}" id="setting-dropdown1">
              <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                  <a class="nav-link {{ (request()->is('store/product/add')) ? 'activate' : '' }}" href="{{route('sel_product')}}">Add Product</a>
                </li>
              	<li class="nav-item">
                  <a class="nav-link {{ (request()->is('store/product/add_multiple_products')) ? 'activate' : '' }}" href="{{route('add_multiple_products')}}">Add Multiple Products</a>
                </li>
 
                <li class="nav-item">
                  <a class="nav-link {{ (request()->is('store/products/price')) ? 'activate' : '' }}" href="{{route('stt_product')}}">Update Price/Mrp</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link {{ (request()->is('store/update/stock')) ? 'activate' : '' }}" href="{{route('st_product')}}">Update Stock</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link {{ (request()->is('store/bulk/upload')) ? 'activate' : '' }}" href="{{route('bulkuprice')}}">Bulk Update Price/Stock</a>
                </li>
               
                </ul>
                </div>
          </li>
          
         
          <li class="nav-item {{ (request()->is('store/settings')) ? 'active' : '' }}">
            <a class="nav-link" href="{{route('settings')}}">
              <i class="material-icons">settings</i>
              <p>Settings</p>
            </a>
          </li>
           
         
        </ul>
      </div>
    </div>