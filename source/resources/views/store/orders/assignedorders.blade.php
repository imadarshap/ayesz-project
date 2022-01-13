@extends('store.layout.app')
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">

<style>
  .collo {
    overflow-y: hidden;
    overflow-x: scroll;
    -webkit-overflow-scrolling: touch;
  }
  .material-icons {
    margin-top: 0px !important;
    margin-bottom: 0px !important;
  }
</style>
@section ('content')
<div class="container-fluid">
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
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header card-header-primary">
        <h4 class="card-title ">Order List (Today)</h4>
      </div>
      <div class="container"> <br>
        <table class="table dataTable " id="myTable">
          <thead>
            <tr>
              <th class="text-center">#</th>
              <th>Cart Id</th>
              <th>Cart price</th>
              <th>User</th>
              <th>Date</th>
              <th>Delivery Boy</th>
              <th>Payment</th>
              <th>Order Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @if(count($ord)>0)
            @php $i=1; @endphp
            @foreach($ord as $order)
            <tr>
              <td class="text-center">{{$i}}</td>
              <td>{{$order->cart_id}}</td>
              <td>{{$order->price_without_delivery}}</td>
              <td>{{$order->user_name}}<br>({{$order->user_phone}})</td>
              <td>{{$order->delivery_date}}</td>
              @if($order->boy_name!= NULL)
              <td>{{$order->boy_name}}
                <p style="font-size:14px">({{$order->boy_phone}})</p>
              </td>
              @else
              <td>Order Not confirmed yet</td>
              @endif
              <td>{{$order->payment_method}}</td>
              <td class="td-actions">
                @if($order->order_status == 'Pending')
                <a href="{{route('store_confirm_order', $order->cart_id)}}" onclick="if(!confirm('Are you sure, you want to confirm this order'))return false;" rel="tooltip" class="btn btn-success btn-sm">
                  Confirm
                </a>
                <a href="{{route('store_reject_order' , $order->cart_id)}}" onclick="if(!confirm('Are you sure, you want to reject this order'))return false;" rel="tooltip" class="btn btn-danger btn-sm">
                  Reject
                </a>
                @elseif($order->order_status == 'Confirmed'||$order->order_status == 'confirmed'||$order->order_status == 'Confirm'||$order->order_status == 'confirm')
                <p style="color:orange !important">Confirmed</p>
                @elseif($order->order_status == 'Out_For_Delivery'||$order->order_status == 'out_for_delivery'||$order->order_status == 'delivery_out'||$order->order_status == 'Delivery_out')
                <p style="color:yellowgreen !important">Out For Delivery</p>
                @elseif($order->order_status == 'completed'||$order->order_status == 'Completed'||$order->order_status == 'Complete'||$order->order_status == 'complete')
                <p style="color:green !important">Completed</p>
                @elseif(strpos($order->order_status,'Rejected') !== false || strpos($order->order_status,'Cancel') !== false)
                <p style="color:red !important">{{$order->order_status}}</p>
                @else
                <p style="color:#2196F3 !important">{{$order->order_status}}</p>
                @endif
              </td>
              <td>
                <a href="{{route('storeShowOrder',$order->cart_id)}}" rel="tooltip" class="btn btn-primary btn-sm">
                <i class="material-icons">layers</i>
                </a>
                <a target="_blank" rel="noopener noreferrer" href="{{route('invoice', $order->cart_id)}}" class="btn btn-success btn-sm">
                  <i class="material-icons">receipt</i>
                </a>
              </td>
            </tr>
            @php $i++; @endphp
            @endforeach
            @else
            <tr>
              <td>No data found</td>
            </tr>
            @endif
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>
<div>
</div>


<!--/////////details model//////////-->
@foreach($ord as $order)
<div class="modal fade" id="exampleModal1{{$order->cart_id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Order Details (<b>{{$order->cart_id}}</b>)</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <!--//form-->
      <table class="table table-bordered" id="example2" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th>product details</th>
            <th>Order_qty</th>
            <th>Price</th>
            @if($order->order_status == 'Pending') <th>I don't have</th> @endif
          </tr>
        </thead>

        <tbody>
          @if(count($details)>0)
          @php $i=1; @endphp

          <tr>
            @foreach($details as $detailss)
            @if($detailss->cart_id==$order->cart_id)

            <td>
              <p><img style="width:25px;height:25px; border-radius:50%" src="{{url($detailss->varient_image)}}" alt="$detailss->product_name"> {{$detailss->product_name}}({{$detailss->quantity}}{{$detailss->unit}})</p>
            </td>
            <td>{{$detailss->qty}}</td>
            <td>
              <p><span style="color:grey">{{$detailss->price}}</span></p>
            </td>
            @if($order->order_status == 'Pending')
            <td align="center">
              <a href="{{route('store_cancel_product', $detailss->store_order_id)}}" rel="tooltip">
                <i class="material-icons" style="color:red">close</i>
              </a>
            </td>
            @endif
            @endif
          </tr>
          @php $i++; @endphp
          @endforeach
          @else
          <tr>
            <td>No data found</td>
          </tr>
          @endif

        </tbody>
      </table>

    </div>
  </div>
</div>
@endforeach

<!--/////////dboy assign model//////////-->
@foreach($ord as $order)
<div class="modal fade" id="exampleModal1{{$order->order_id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Dboy Assign (<b>{{$order->cart_id}}</b>)</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <!--//form-->
      <form class="forms-sample" action="{{route('store_confirm_order', $order->cart_id)}}" method="post" enctype="multipart/form-data">
        {{csrf_field()}}
        <div class="row">
          <div class="col-md-3" align="center"></div>
          <div class="col-md-6" align="center">
            <div class="form-group">
              <select name="dboy_id" class="form-control">
                <option disabled selected>Select Delivery boy</option>
                @foreach($nearbydboy as $nearbydboys)
                <option value="{{$nearbydboys->dboy_id}}">{{$nearbydboys->boy_name}}({{$nearbydboys->distance}} KM away)</option>
                @endforeach
              </select>
            </div>
            <button type="submit" class="btn btn-primary pull-center">Submit</button>
          </div>
        </div>

        <div class="clearfix"></div>
      </form>
      <!--//form-->
    </div>
  </div>
</div>
@endforeach
<script>
  $(document).ready(function() {
    $('#myTable').DataTable();
  });
</script>
@endsection
</div>