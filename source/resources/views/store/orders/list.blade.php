@extends('store.layout.app')
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="{{url('assets/css/plugins/jquery.multiselect.css')}}">
<style>
    .material-icons {
        margin-top: 0px !important;
        margin-bottom: 0px !important;
    }
</style>
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
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title ">{{$title}}</h4>
                </div>
                <div class="container"> <br>
                        <form id="form_report" mehtod="get" action="">
                    <div class="row">
                            <div class="col-md-4">
                                <div class="form">
                                    <label class="bmd-label-floating">Select Order Status</label>
                                    <select name="order_status[]" id="order_status" class="form-input" multiple>
                                        <option value="Pending" @if(!empty($request->order_status)&&in_array("Pending",$request->order_status)) selected="" @endif>Pending</option>
                                        <option value="Confirmed" @if(!empty($request->order_status)&&in_array("Confirmed",$request->order_status)) selected="" @endif>Confirmed By Vendor</option>
                                        <option value="Rejected_By_Vendor" @if(!empty($request->order_status)&&in_array("Rejected_By_Vendor",$request->order_status)) selected="" @endif>Rejected By Vendor</option>
                                        <option value="Accepted_By_Delivery_Agent" @if(!empty($request->order_status)&&in_array("Accepted_By_Delivery_Agent",$request->order_status)) selected="" @endif>Accepted By Delivery Agent</option>
                                        <option value="Rejected_By_Delivery_Agent" @if(!empty($request->order_status)&&in_array("Rejected_By_Delivery_Agent",$request->order_status)) selected="" @endif>Rejected By Delivery Agent</option>
                                        <option value="Out_For_Delivery" @if(!empty($request->order_status)&&in_array("Out_For_Delivery",$request->order_status)) selected="" @endif>Out For Delivery</option>
                                        <option value="Completed" @if(!empty($request->order_status)&&in_array("Completed",$request->order_status)) selected="" @endif>Completed</option>
                                        <option value="Cancelled" @if(!empty($request->order_status)&&in_array("Cancelled",$request->order_status)) selected="" @endif>Cancelled</option>
                                        <option value="Rejected" @if(!empty($request->order_status)&&in_array("Rejected",$request->order_status)) selected="" @endif>Rejected</option>
                                        <!--<option value="Out_For_Delivery" @if(!empty($request->order_status)&&in_array("Out_For_Delivery",$request->order_status)) selected="" @endif>Out For Delivery</option>-->
                                        <option value="Failed" @if(!empty($request->order_status)&&in_array("Failed",$request->order_status)) selected="" @endif>Failed</option>
                                    </select>
                                </div>
                            </div>
                    </div>
                    <div class="row">
                        <div class="col">
                        <button type="submit" class="btn btn-primary pull-center mt-2">Submit</button></div>
                    </div>
                        </form>
                        <hr>
                    <table class="display" id="myTable" data-ordering="false">
                        <thead>
                            <tr>
                                <th>Cart ID</th>
                                <th>Price</th>
                                <th>User</th>
                                <th>Store</th>
                                <th>Agent</th>
                                <th>Order Date</th>
                                <th>Status</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // $('#myTable').DataTable();
        $('select[multiple]').multiselect({
            columns: 1,
            search: true,
            selectAll: true,
        });
        $('#myTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": "{{route('storeGetOrders')}}?order_status=@if(!empty($request->order_status)){{implode(',',$request->order_status)}}@endif",
            "drawCallback": function(settings) {

            },
            columns: [
                {
                    data: 'cart_id'
                },
                {
                    data: 'price_without_delivery',
                    render: function(price_without_delivery) {
                        return "{{$currency}}" + price_without_delivery
                    }
                },
                {
                    data: {
                        user_name: 'user_name',
                        user_phone: 'user_phone'
                    },
                    render: function(data) {
                        return data.user_name + '<p style="font-size:14px;margin-bottom:0px">(' + data.user_phone + ')</p>';
                    }
                },
                {
                    data: {
                        store_name: 'store_name',
                        store_phone: 'store_phone'
                    },
                    render: function(data) {
                        return data.store_name + '<p style="font-size:14px;margin-bottom:0px">(' + data.store_phone + ')</p>';
                    }
                },
                {
                    data: {
                        boy_name: 'boy_name',
                        boy_phone: 'boy_phone'
                    },
                    render: function(data) {
                        if (data.boy_name != '')
                            return data.boy_name + '<p style="font-size:14px;margin-bottom:0px">(' + data.boy_phone + ')</p>';
                        else
                            return '-';
                    }
                },
                {
                    data: 'created_at'
                },
                {
                    data: {order_status:'order_status',cart_id:'cart_id'},
                    render:function(data){
                        if(data.order_status == 'Pending')
                            return '<a href="{{url("store/order/confirm/r")}}/'+data.cart_id+'" onclick="if(!confirm("Are you sure, you want to confirm this order"))return false;" rel="tooltip" class="btn btn-success btn-sm">Confirm</a>'
                                +'<a href="{{url("store/orders/reject")}}/'+data.cart_id+'" onclick="if(!confirm("Are you sure, you want to reject this order"))return false;" rel="tooltip" class="btn btn-danger btn-sm">Reject</a>';
                        else if(data.order_status == 'Confirmed'||data.order_status == 'confirmed'||data.order_status == 'Confirm'||data.order_status == 'confirm')
                            return'<p style="color:orange !important">Confirmed</p>';
                        else if(data.order_status == 'Out_For_Delivery'||data.order_status == 'out_for_delivery'||data.order_status == 'delivery_out'||data.order_status == 'Delivery_out')
                            return '<p style="color:yellowgreen !important">Out For Delivery</p>';
                        else if(data.order_status == 'completed'||data.order_status == 'Completed'||data.order_status == 'Complete'||data.order_status == 'complete')
                            return '<p style="color:green !important">Completed</p>';
                        else if(data.order_status.indexOf('Rejected') !=-1 || data.order_status.indexOf('Cancel')!=-1)
                            return '<p style="color:red !important">'+data.order_status+'</p>';
                        else
                            return '<p style="color:#2196F3 !important">'+data.order_status+'</p>';
                    }
                },
                {
                    data: 'cart_id',
                    sortable: false,
                    searchable: false,
                    render: function(cart_id) {
                        return '<div class="text-right">'+
                            '<a href="' + "{{url('store/orders/show')}}/" + cart_id + '" rel="tooltip" class="btn btn-primary btn-sm">' +
                            '<i class="material-icons">layers</i>' +
                            '</a></div>';
                    }
                }
            ]
        });
    });
</script>
@endsection
</div>