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
                      <div class="col-md-3" style="display: none;">
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
                      <br>
                      <div class="col-md-4">
                        <div class="form">
                          <label class="bmd-label-floating">Vendors</label>
                        <select name="stores[]" id="stores" class="form-control" multiple>
                        	<option value="">-None-</option>
                        	@if(!empty($stores))
                        	@foreach($stores as $store)
                        		<option value="{{$store->store_id}}" @if(!empty($request->stores)&&in_array($store->store_id,$request->stores)) selected="" @endif>{{$store->store_name}}</option>
                        	@endforeach
                        	@endif
                        </select>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form">
                          <label class="bmd-label-floating">Category</label>
                        <select name="cat_id[]" id="cat_id" class="form-control" multiple>
                            <option value="">-None-</option>
                            @if(!empty($adminTopApp))
                            @foreach($adminTopApp as $cat)
                                <option value="{{$cat->cat_id}}" @if(!empty($request->cat_id)&&in_array($cat->cat_id,$request->cat_id)) selected="" @endif>{{$cat->title}}</option>
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
    <?php
        $city = $request->city;
        $stores_selected = $request->stores;
        $cat = $request->cat_id;
        $fromDate = $request->fromdate;
        $toDate = $request->todate;
        $store = '';

        if (!empty($stores_selected)) {

            if(!empty($fromDate)&&!empty($toDate)){
                // DB::enableQueryLog();
                $store= DB::table('store_products')
                    ->whereIn('store_id',$stores_selected)
                    ->where('created_at','>=',$fromDate.' 00:00:00')
                    ->where('created_at','<=',$toDate.' 24:00:00')
                    ->get();
                    // $query = DB::getQueryLog();
                    // print_r($query);
                // echo '<pre>'; print_r($store); echo '</pre>';
            } else {
                $store= DB::table('store_products')
                    ->whereIn('store_id',$stores_selected)
                    ->get();
            }
        }

    ?>
<div class="card-header card-header-primary">
      <h4 class="card-title ">Report Product List</h4>
    </div>
<div class="container" style="overflow-x:scroll"> <br> 

<table class="display datatable table-bordered" id="myTable">
    <thead>
        <tr>
            <th>#</th>
        	<th>City</th>
        	
        	<th>Category</th>
            <th>Product Name</th>
             <th>Qty</th>
            <th>UOM</th>
            <th>MRP</th>
        	<th>Price</th>
        	<th>Vendor</th>
        	
        	<th>Date Of Create</th>
        </tr>
    </thead>
    <tbody>
           
    <?php
    if(!empty($store)){ 
        foreach ($store as $key => $value) { 
            $storename = '';
            if (!empty($value->store_id)) {
                $storename = DB::table('store')
                   ->where('store_id',$value->store_id)
                   ->first();
            }
            $varientname = '';
            if(!empty($value->varient_id)){
                $varientname = DB::table('product_varient')
                   ->where('varient_id',$value->varient_id)
                   ->first();
            }
            $productname = '';
            if(!empty($varientname->product_id)){
                $productname = DB::table('product')
                   ->where('product_id',$varientname->product_id)
                   ->first();
            }
            $categoryname = '';
            if(!empty($productname->cat_id)){
                $categoryname = DB::table('categories')
                   ->where('cat_id',$productname->cat_id)
                   ->first();
            }
            if (!empty($cat) && !empty($categoryname->title) && !empty($productname->product_name)) {
            if (in_array($productname->cat_id, $cat)){
        ?>
        <tr>
            <td class="text-center"><?php echo $key+1; ?></td>
            <td><?php if(!empty($city)){  echo $city; } ?></td>
             <td><?php  if(!empty($categoryname->title)){  echo $categoryname->title; } ?></td>
            <td><?php if(!empty($productname->product_name)){  echo $productname->product_name; } ?></td>
            <td><?php if(!empty($value->quantity)){  echo $value->quantity; } ?></td>
            <td><?php if(!empty($varientname->unit)){ echo $varientname->unit; } ?></td>
       
            <td><?php if(!empty($value->mrp)){  echo $value->mrp; } ?></td>
             <td><?php if(!empty($value->price)){  echo $value->price; } ?></td>
            <td>#<?php if(!empty($storename->store_name)){  echo $storename->store_id; } ?> <?php echo $storename->store_name; ?></td>
           
            <td><?php if(!empty($value->created_at)){  echo $value->created_at; } ?></td>
        </tr>

          <?php  }
        }else{

              if (!empty($categoryname->title) && !empty($productname->product_name)) {      
        ?>
        <tr>
            <td class="text-center"><?php echo $key+1; ?></td>
            <td><?php if(!empty($city)){  echo $city; } ?></td>
            <td>#<?php if(!empty($storename->store_name)){  echo $storename->store_id; } ?> <?php echo $storename->store_name; ?></td>
            <td><?php if(!empty($productname->product_name)){  echo $productname->product_name; } ?></td>
                    <td><?php if(!empty($value->quantity)){  echo $value->quantity; } ?></td>
            <td><?php if(!empty($varientname->unit)){ echo $varientname->unit; } ?></td>
            <!--<td><?php if(!empty($varientname->base_mrp)){  echo $varientname->base_mrp; } ?></td>-->
              <td><?php if(!empty($value->mrp)){  echo $value->mrp; } ?></td>
            <td><?php if(!empty($value->price)){  echo $value->price; } ?></td>
            <td><?php  if(!empty($categoryname->title)){  echo $categoryname->title; } ?></td>
            <td><?php if(!empty($value->created_at)){  echo $value->created_at; } ?></td>
        </tr>
    <?php } } } }else{ ?>
          

                    <tr>
                      <td class="text-center" colspan="14">No data found</td>
                    </tr>
                <?php } ?>

    </tbody>
	<tfoot>
        <tr>
            <th>#</th>
        	<th>City</th>
        	
        	<th>Category</th>
            <th>Product Name</th>
               <th>Qty</th>
            <th>UOM</th>
            <th>MRP</th>
        	<th>Price</th>
        	<th>Vendor</th>
        	
        	<th>Date Of Create</th>
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
        	var report_title = 'Product Report From {{$request->fromdate}} to {{$request->todate}}';
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
