@extends('admin.layout.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section ('content')
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
<link href="{{url('assets/css/plugins/glyphicon.min.css')}}" rel="stylesheet"/>
<style>
.loader {
	display:none;
    overflow: hidden;
    width: 100%;
    height: 4px;
    background-color: #B3E5FC;
    margin-top: 10px;
}

.indeterminate {
    position: relative;
    width: 100%;
    height: 100%;
}

.indeterminate:before {
    content: '';
    position: absolute;
    height: 100%;
    background-color: #03A9F4;
    animation: indeterminate_first 1.5s infinite ease-out;
}

.indeterminate:after {
    content: '';
    position: absolute;
    height: 100%;
    background-color: #4FC3F7;
    animation: indeterminate_second 1.5s infinite ease-in;
}

@keyframes indeterminate_first {
    0% {
        left: -100%;
        width: 100%;
    }
    100% {
        left: 100%;
        width: 10%;
    }
}

@keyframes indeterminate_second {
    0% {
        left: -150%;
        width: 100%;
    }
    100% {
        left: 100%;
        width: 10%;
    }
}
    .material-icons{
        margin-top:0px !important;
        margin-bottom:0px !important;
    }
.bootstrap-select button.btn.dropdown-toggle.btn-light,.form-input {
	    position: relative;
    width: 100%;
    text-align: left;
    border: 1px solid #aaa;
    background-color: #fff;
    padding: 7.5px 7px 7.5px 5px;
    margin-top: 2px;
    font-size: 13px;
    color: #555;
    outline-offset: -2px;
    white-space: nowrap;
    border-radius: 0px;
    text-transform: none;
    box-shadow: none;
}
.bootstrap-select .dropdown-toggle .filter-option-inner-inner {
    margin-right: 10px;
}
.dropdown.bootstrap-select{
	width:250px !important;
}
.bootstrap-select button.btn.dropdown-toggle.btn-light{
    padding: 10px 7.5px;
}
</style>
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
<!--      <a href="{{route('city')}}" class="btn btn-primary ml-auto" style="width:10%;float:right;padding: 3px 0px 3px 0px;"><i class="material-icons">add</i>Add City</a> -->
</div>
</div>
</div>
<div class="container-fluid">
<div class="row">
<div class="col-lg-12">
	<div class="card">
    <div class="card-header card-header-primary">
      <h4 class="card-title ">Show Report By</h4>
    </div>
	<div class="container">
    <br>
    <form id="form_report" mehtod="get" action="">
                    {{csrf_field()}}
    				<div class="row">
                      <div class="col-md-3">
                        <div class="form">
                          <label class="bmd-label-floating">City</label>
                        <select name="city" id="city" class="selectpicker" data-live-search="true">
                        	<option value="">-None-</option>
                        	@foreach($cities as $city)
                        		<option value="{{$city->city_name}}" @if($request->city==$city->city_name) selected="" @endif>{{$city->city_name}}</option>
                 			@endforeach
                        </select>
                        </div>
                      </div>
                    <div class="col-md-3">
                        <div class="form">
                          <label class="bmd-label-floating">Order Status</label>
                        <select name="order_status[]" id="order_status" class="form-input" multiple>
                        	<option value="">-None-</option>
	                        <option value="Pending" @if(!empty($request->order_status)&&in_array("Pending",$request->order_status)) selected="" @endif>Pending</option>
                        <option value="Confirmed" @if(!empty($request->order_status)&&in_array("Confirmed",$request->order_status)) selected="" @endif>Confirmed</option>
                        <option value="Out_For_Delivery" @if(!empty($request->order_status)&&in_array("Out_For_Delivery",$request->order_status)) selected="" @endif>Out For Delivery</option>
                        <option value="Completed" @if(!empty($request->order_status)&&in_array("Completed",$request->order_status)) selected="" @endif>Completed</option>
                        <option value="Cancelled" @if(!empty($request->order_status)&&in_array("Cancelled",$request->order_status)) selected="" @endif>Cancelled</option>
                        <option value="Rejected" @if(!empty($request->order_status)&&in_array("Rejected",$request->order_status)) selected="" @endif>Rejected</option>
                        <option value="Failed" @if(!empty($request->order_status)&&in_array("Failed",$request->order_status)) selected="" @endif>Failed</option>

                        </select>
                        </div>
                      </div>
                    	
                      <div class="col-md-4">
                        <div class="form">
                          <label class="bmd-label-floating">Delivery Agent</label>
                        <select name="delivery_agents[]" id="delivery_boy" class="form-control" multiple>
                        	<option value="">-None-</option>
                        	@if(!empty($dboys))
                        	@foreach($dboys as $dboy)
                        		<option value="{{$dboy->dboy_id}}" @if(!empty($request->delivery_agents) && in_array($dboy->dboy_id,$request->delivery_agents)) selected="" @endif>{{$dboy->boy_name}}</option>
                        	@endforeach
                        	@endif
                        </select>
                        </div>
                      </div>
                    </div>
    				<div class="row">
                      <div class="col-md-3">
                        <div class="form">
                          <label class="bmd-label-floating">From Date</label>
                        <input autocomplete="off" type="text" name="fromdate" value="{{$request->fromdate}}" id="fromdate" class="form-input datepicker" placeholder="Select a date"/>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form">
                          <label class="bmd-label-floating">To Date</label>
                        <input autocomplete="off" type="text" name="todate" id="todate" value="{{$request->todate}}" class="form-input datepicker" placeholder="Select a date"/>
                        </div>
                      </div>
    				</div>
                    <button type="submit" class="btn btn-primary pull-center">Submit</button>
    				<div class="loader">
    					<div class="indeterminate"></div>
					</div>
    </form>
    <br>
	</div>
</div>

<div class="row">
<div class="col-lg-12">
   
<div class="card">
<div class="card-header card-header-primary">
      <h4 class="card-title ">Report</h4>
    </div>
<div class="container" style="overflow-x:scroll"> <br> 

<table class="display datatable table-bordered" id="myTable">
    <thead>
        <tr>
            <th>#</th>
        	<th>City</th>
            <th>Order Id</th>
        	<th>Order Date</th>
        	<th>Delivery Boy</th>
        	<th>Status</th>
        	<th>PaymentMode</th>
        </tr>
    </thead>
    <tbody>
           @if(!empty($report['orders']))
          @php $i=1; @endphp
          @foreach($report['orders'] as $order)
    	  @php 
    		$tax = (($order->price_without_delivery-$order->commission)/100)*5; 
    		$convience_fee = (($order->price_without_delivery-$order->commission)/100)*1.84;
    		$payble_amount = $order->price_without_delivery - $tax - $convience_fee;
    		@endphp
        <tr>
            <td class="text-center">{{$i}}</td>
        	<td>{{$order->city}}</td>
            <td>#{{$order->cart_id}}</td>
        	<td>{{$order->order_date}}</td>
        	<td>#{{$order->dboy_id}} - {{$order->boy_name}}</td>
        	<td>{{$order->order_status}}</td>
        	<td>{{$order->payment_method}}</td>
        </tr>
          @php $i++; @endphp
                 @endforeach
                  @else
                    <tr>
                      <td class="text-center" colspan="14">No data found</td>
                    </tr>
                  @endif
    </tbody>
	<tfoot>
        <tr>
            <th>#</th>
        	<th>City</th>
            <th>Order Id</th>
        	<th>Order Date</th>
        	<th>Delivery Boy</th>
        	<th>Status</th>
        	<th>PaymentMode</th>
        </tr>
    </tfoot>
</table>
<br/>
</div>  
</div>
</div>
</div>
</div>
<div>
    </div>
     <script>
     	function setMultipleSelect(){
        	$('select[multiple]').multiselect({
    			columns  : 1,
    			search   : true,
    			selectAll: true,
			});
        }
       	$(document).ready(function(){
        	$('#fromdate').datetimepicker({
            	useCurrent: false,
            	maxDate: new Date(),
		        format:'YYYY-MM-DD',
			});
        
        @if(!empty($request->fromdate))
        	$('#fromdate').data("DateTimePicker").date('{{$request->fromdate}}');
        @endif
        
       $('#todate').datetimepicker({
   		useCurrent: false,
       	maxDate: new Date(),
       	format:'YYYY-MM-DD',
   		});
       $("#fromdate").on("dp.change", function (e) {
           $('#todate').data("DateTimePicker").minDate(e.date);
       });
       $("#todate").on("dp.change", function (e) {
           $('#fromdate').data("DateTimePicker").maxDate(e.date);
       });
        
        	@if(!empty($report['orders']))
        	var report_title = 'Delivery Agent\'s Order Status Report From {{$request->fromdate}} to {{$request->todate}}';
        	$('#myTable').DataTable( {
        		dom: 'Bfrtip',
        		buttons: [
            		'copy', 
                	{
	                    extend: 'csv',
	                    title: report_title
	                },
                	{
	                    extend: 'excel',
	                    title: report_title
	                },
                	{
                    	extend: 'pdfHtml5',
                        title: report_title,
                		orientation: 'landscape',
                		pageSize: 'LEGAL'
                    },{
        				extend: 'print',
                    	title: report_title,
        				columns: ':not(.select-checkbox)',
        				orientation: 'landscape'
    				}
        		]
		    } );
        	@endif
        	setMultipleSelect();
        	$('#city').change(function(){
            	 $('select[multiple]').multiselect('disable',true);
                 $('.loader').show();
            	$.ajax({
	  				type: "GET",
					url: '{{route('get_stores_dboy_by_city')}}'+'?city='+$(this).val(),
                	dataType:'json',
	  				success: function(data){
                    console.log(data);
                    	var stores = '';
                    	$.each(data.stores,function(key,value){
                        	stores += '<option value="'+value.store_id+'">'+value.store_name+'</option>';
                        });
                    	$('#stores').html(stores);
                    	var dboys = '';
                    	$.each(data.dboys,function(key,value){
                        	dboys += '<option value="'+value.dboy_id+'">'+value.boy_name+'</option>';
                        });
                    	$('#delivery_boy').html(dboys);
                    	$('select[multiple]').multiselect('reload');
                    	$('select[multiple]').multiselect('disable',false);
                    	$('.loader').hide();
                    	// setMultipleSelect();
                	}
				});
            });
				// $('#form_report').on('submit',function(e){
				// e.preventDefault();
				// $('.loader').show();
				// $.ajax({
				// type: "POST",
				// 	url: '{{route('getreport')}}',
				// data: new FormData(this),
				// dataType:'json',
				// contentType: false,
				// 	processData: false,
				// headers: {
				// 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				// },
				// success: function(data){
				// console.log(data);
				// $('.loader').hide();
				// }
				// });
				// });
		});
    </script>
    @endsection
</div>
