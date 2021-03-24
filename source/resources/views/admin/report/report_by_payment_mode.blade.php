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
	height: 42px;
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
                          <label class="bmd-label-floating">Payment Mode</label>
                        <select name="payment_modes[]" id="payment_mode" class="form-input" multiple>
                        	<option value="">-None-</option>
	                        <option value="cod" @if(!empty($request->payment_modes)&&in_array("cod",$request->payment_modes)) selected="" @endif>COD</option>
                        	<option value="card" @if(!empty($request->payment_modes)&&in_array("card",$request->payment_modes)) selected=""@endif>Card</option>
                        	<option value="upi" @if(!empty($request->payment_modes)&&in_array("upi",$request->payment_modes)) selected=""@endif>UPI</option>
                        </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form">
                          <label class="bmd-label-floating">From Date</label>
                        <input type="text" name="fromdate" value="{{$request->fromdate}}" id="fromdate" class="form-input datepicker" placeholder="Select a date"/>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form">
                          <label class="bmd-label-floating">To Date</label>
                        <input type="text" name="todate" id="todate" value="{{$request->todate}}" class="form-input datepicker" placeholder="Select a date"/>
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
            <th>Order Id</th>
        	<th>Order Date</th>
       		<th>Order Amount</th>
        	<th>Payment Mode</th>
        	<th>City</th>
        	<th>Status</th>
        </tr>
    </thead>
    <tbody>
           @if(!empty($report['orders']))
          @php $i=1; @endphp
          @foreach($report['orders'] as $order)
        <tr>
            <td class="text-center">{{$i}}</td>
            <td>#{{$order->cart_id}}</td>
        	<td>{{$order->order_date}}</td>
        	<td>{{$order->total_price}}</td>
        	<td>{{$order->payment_method}}</td>
        	<td>{{$order->city}}</td>
        	<td>{{$order->order_status}}</td>
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
            <th>Order Id</th>
        	<th>Order Date</th>
       		<th>Order Amount</th>
        	<th>Payment Mode</th>
        	<th>City</th>
        	<th>Status</th>
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
        	var report_title = 'Payment Mode Report From {{$request->fromdate}} to {{$request->todate}}';
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
		});
    </script>
    @endsection
</div>
