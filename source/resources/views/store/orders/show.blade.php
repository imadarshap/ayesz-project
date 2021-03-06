@extends('store.layout.app')

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
          <h4 class="card-title">Order Details</h4>
          <form class="forms-sample" action="{{route('updateOrder', $order->order_id)}}" method="post" enctype="multipart/form-data">
            {{csrf_field()}}
        </div>
        <div class="card-body mt-4">

          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label>Cart ID</label>
                <div><b>#{{$order->cart_id}}</b></div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Order Date</label>
                <div><b>{{date('d M Y, h:i:s a',strtotime($order->created_at))}}</b></div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Payment Method</label>
                <div><b>{{strtoupper($order->payment_method)}} (<span class="text-bold">{{strtoupper($order->payment_status)}}</span>)</b></div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Order Status</label>
                <div><b>{{str_replace('_',' ',$order->order_status)}}</b></div>
              </div>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Customer Details</label>
                <div class="text-bold">
                  <strong>{{$user->user_name}}</strong> ({{$user->user_phone}})
                  <div>{{$user->user_email}}</div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Address Details</label>
                <div class="text-bold">
                  <strong>{{$address->receiver_name}}</strong><br>
                  {{$address->house_no}}, {{$address->society}}, {{$address->landmark}}, {{$address->city}}, {{$address->state}}, {{$address->pincode}}
                  <br />Contact - {{$address->receiver_phone}}
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Delivery Agent</label>
                @if(!empty($dboy))
                <div class="text-bold">
                  <strong>{{$dboy->boy_name}}</strong> ({{$dboy->boy_phone}})
                </div>
                @else
                <div>None</div>
                @endif
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
                  <td>{{$currency}}{{$item->price}}</td>
                </tr>
                @endforeach
                <tr>
                  <th>Item Total</th>
                  <th>{{$currency}}<span id="item_total_price">{{$order->price_without_delivery}}</span></th>
                </tr>
                <tr>
                  <td>Delivery Charge</td>
                  <td>
                  {{$currency}}{{$order->delivery_charge}}
                  </td>
                </tr>
                <tr>
                  <th>Grand Total</th>
                  <th>{{$currency}}<span id="grand_total">{{$order->total_price}}</span></th>
                </tr>
                <tr>
                  <td>Paid By Wallet</td>
                  <td>{{$currency}}<span id="paid_by_wallet">{{$order->paid_by_wallet}}</span></td>
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
        placeholder: 'Select Vendors',
        search: 'Search Vendors'
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
      $('#rem_price').html(grandTotal-parseFloat($('#paid_by_wallet').html()));
    });
    $('#delivery_charge').on('input',function(){
      if($(this).val().trim() == '')
        $(this).val('0');
      var total_price = $('#item_total_price').html();
      var grandTotal = parseFloat(total_price)+parseFloat($(this).val());
      $('#grand_total').html(grandTotal);
      $('#rem_price').html(grandTotal-parseFloat($('#paid_by_wallet').html()));
    });
  });
</script>
@endsection