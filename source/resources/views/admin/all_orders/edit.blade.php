@extends('admin.layout.app')

@section ('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-lg-12">
      @if (session()->has('success'))
      <div class="alert alert-success">
        @if(is_array(session()->get('success')))
        <ul>
          @foreach (session()->get('success') as $message)
          <li>{{ $message }}</li>
          @endforeach
        </ul>
        @else
        {{ session()->get('success') }}
        @endif
      </div>
      @endif
      @if (count($errors) > 0)
      @if($errors->any())
      <div class="alert alert-danger" role="alert">
        {{$errors->first()}}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      @endif
      @endif
    </div>
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-primary">
          <h4 class="card-title">Edit Order</h4>
          <form class="forms-sample" action="{{route('updateOrder', $order->order_id)}}" method="post" enctype="multipart/form-data">
            {{csrf_field()}}
        </div>
        <div class="card-body mt-4">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label class="bmd-label-floating">Order ID</label>
                <div><b>{{$order->order_id}}</b></div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label class="bmd-label-floating">Cart ID</label>
                <div><b>#{{$order->cart_id}}</b></div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label class="bmd-label-floating">Order Date</label>
                <div><b>{{date('d M Y',strtotime($order->order_date))}}</b></div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label class="bmd-label-floating">Payment Method</label>
                <div><b>{{strtoupper($order->payment_method)}} (<span class="text-bold">{{strtoupper($order->payment_status)}}</span>)</b></div>
              </div>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label class="bmd-label-floating">Store Details</label>
                <div class="text-bold">
                  <strong>{{$store->store_name}}</strong> ({{$store->phone_number}})
                  <div>{{$store->email}}</div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label class="bmd-label-floating">Customer Details</label>
                <div class="text-bold">
                  <strong>{{$user->user_name}}</strong> ({{$user->user_phone}})
                  <div>{{$user->user_email}}</div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label class="bmd-label-floating">Address Details</label>
                <div class="text-bold">
                  <strong>{{$address->receiver_name}}</strong><br>
                  {{$address->house_no}}, {{$address->society}}, {{$address->landmark}}, {{$address->city}}, {{$address->state}}, {{$address->pincode}}
                  <br />Contact - {{$address->receiver_phone}}
                </div>
              </div>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label class="bmd-label-floating">Delivery Agent</label>
                <select name="dboy_id" class="form-control">
                  <option value="0">Select Delivery Agent</option>
                  @foreach($dboys as $dboy)
                  <option @if($order->dboy_id == $dboy->dboy_id) selected @endif value="{{$dboy->dboy_id}}">
                    {{$dboy->boy_name}} ({{$dboy->boy_phone}})
                  </option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form">
                <label class="bmd-label-floating">Select Order Status</label>
                <select name="order_status" id="order_status" class="form-control" required>
                  <option value="Pending" @if(!empty($order->order_status)&& $order->order_status == "Pending") selected="" @endif>Pending</option>
                  <option value="Confirmed" @if(!empty($order->order_status)&& $order->order_status == "Confirmed") selected="" @endif>Confirmed By Vendor</option>
                  <option value="Rejected_By_Vendor" @if(!empty($order->order_status)&& $order->order_status =="Rejected_By_Vendor") selected="" @endif>Rejected By Vendor</option>
                  <option value="Accepted_By_Delivery_Agent" @if(!empty($order->order_status)&& $order->order_status == "Accepted_By_Delivery_Agent") selected="" @endif>Accepted By Delivery Agent</option>
                  <option value="Rejected_By_Delivery_Agent" @if(!empty($order->order_status)&& $order->order_status == "Rejected_By_Delivery_Agent") selected="" @endif>Rejected By Delivery Agent</option>
                  <option value="Out_For_Delivery" @if(!empty($order->order_status)&& $order->order_status == "Out_For_Delivery") selected="" @endif>Out For Delivery</option>
                  <option value="Completed" @if(!empty($order->order_status)&& $order->order_status == "Completed") selected="" @endif>Completed</option>
                  <option value="Cancelled" @if(!empty($order->order_status)&& $order->order_status == "Cancelled") selected="" @endif>Cancelled</option>
                  <option value="Rejected" @if(!empty($order->order_status)&& $order->order_status == "Rejected") selected="" @endif>Rejected</option>
                  <option value="Failed" @if(!empty($order->order_status)&& $order->order_status == "Failed") selected="" @endif>Failed</option>
                </select>
              </div>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-md-5">    
              <h3><b>Order Billing</b></h3>
              <table class="table table-bordered">
                <tr>
                <th colspan="2">Order Items</th>
                </tr>
                @foreach($items as $item)
                <tr>
                  <td>{{$item->qty}} X {{$item->product_name}} {{$item->quantity}}{{$item->unit}} </td>
                  <td>{{$currency}} <input type="number" class="item_price" name="item_price-{{$item->store_order_id}}" value="{{$item->price}}" required/></td>
                </tr>
                @endforeach
                <tr>
                  <th>Item Total</th>
                  <th>{{$currency}}<span id="item_total_price">{{$order->price_without_delivery}}</span></th>
                </tr>
                <tr>
                  <td>Delivery Charge</td>
                  <td>
                  {{$currency}} <input type="number" id="delivery_charge" name="delivery_charge" value="{{$order->delivery_charge}}" required />
                  </td>
                </tr>
                <tr>
                  <th>Grand Total</th>
                  <th>{{$currency}}<span id="grand_total">{{$order->total_price}}</span></th>
                </tr>
                <tr>
                  <td>Paid By Wallet</td>
                  <td>{{$currency}} <input type="number" id="paid_by_wallet" name="paid_by_wallet" value="{{$order->paid_by_wallet}}" required /></td>
                </tr>
                <tr>
                  <td>Discount</td>
                  <td>-{{$currency}}<span id="discount">{{$order->coupon_discount}}</span></td>
                </tr>
                <tr class="bg-primary text-white">
                  <th>Payble Amount</th>
                  <th>{{$currency}}<span id="rem_price">{{$order->rem_price}}</span></th>
                </tr>
              </table>
            </div>
            <div class="col-md-7">
              <h3><b>Order Logs</b></h3>
              <table class="table table-striped">
                <thead>
                  <th>Date</th>
                  <th>Admin</th>
                  <th>Log</th>
                </thead>
                <tbody>
                  @foreach($logs as $log)
                  <tr>
                    <td>{{date('d M Y, h:i:s a',strtotime($log->created_at))}}</td>
                    <td>#{{$log->admin_id}}-{{$log->admin_name}} ({{$log->admin_email}})</td>
                    <td>{{$log->log}}</td>
                  </tr>
                  @endforeach
                  
                </tbody>
              </table>
            </div>
          </div>
          <div class="mt-3"> 
          <button type="submit" class="btn btn-primary pull-center">Save Changes</button>
          <a href="{{route('allOrders')}}" class="btn">Close</a>
          </div>
          <div class="clearfix"></div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  $(document).ready(function() {
    $('.dataTable').DataTable();

    $('select[multiple]').multiselect({
      columns: 1,
      search: true,
      selectAll: true,
      texts: {
        placeholder: 'Select Order Status',
        search: 'Search Order Status'
      }
    });

    $('.item_price').on('input',function(){
      if($(this).val().trim() == '')
        $(this).val('0');
        console.log('changed');
      var total_price = 0;
      $(".item_price").each(function() {
          total_price += parseFloat($(this).val());
      });
      $('#item_total_price').html(total_price);
      var grandTotal = total_price+parseFloat($('#delivery_charge').val());
      $('#grand_total').html(grandTotal);
      $('#rem_price').html(grandTotal-parseFloat($('#paid_by_wallet').val()));
    });

    $('#paid_by_wallet').on('input',function(){
      if($(this).val().trim() == '')
        $(this).val('0');
      var grandTotal = $('#grand_total').html();
      var remPrice = parseFloat(grandTotal)-parseFloat($(this).val());
      $('#rem_price').html(remPrice);
      if(parseFloat($('#rem_price').html())<0){
        $(this).val('0');
        var total_price = 0;
        $(".item_price").each(function() {
          total_price += parseFloat($(this).val());
        });
        var grandTotal = total_price+parseFloat($('#delivery_charge').val());
        $('#grand_total').html(grandTotal);
        $('#rem_price').html(grandTotal-parseFloat($('#paid_by_wallet').val()));
      }
    });

    $('#delivery_charge').on('input',function(){
      if($(this).val().trim() == '')
        $(this).val('0');
      var total_price = $('#item_total_price').html();
      var grandTotal = parseFloat(total_price)+parseFloat($(this).val());
      $('#grand_total').html(grandTotal);
      $('#rem_price').html(grandTotal-parseFloat($('#paid_by_wallet').val()));
    });
  });
</script>
@endsection