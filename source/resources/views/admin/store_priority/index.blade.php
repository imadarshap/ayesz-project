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
    border: 3px solid #f3f3f3;
    border-radius: 50%;
    border-top: 3px solid #3498db;
    width: 25px;
    height: 25px;
    -webkit-animation: spin 2s linear infinite;
    animation: spin 2s linear infinite;
    display: inline-flex;
	opacity:0;
}

/* Safari */
@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
#snackbar {
  visibility: hidden; /* Hidden by default. Visible on click */
  min-width: 250px; /* Set a default minimum width */
  margin-left: -125px; /* Divide value of min-width by 2 */
  background-color: #333; /* Black background color */
  color: #fff; /* White text color */
  text-align: center; /* Centered text */
  border-radius: 2px; /* Rounded borders */
  padding: 16px; /* Padding */
  position: fixed; /* Sit on top of the screen */
  z-index: 1; /* Add a z-index if needed */
  left: 50%; /* Center the snackbar */
  bottom: 30px; /* 30px from the bottom */
}

/* Show the snackbar when clicking on a button (class added with JavaScript) */
#snackbar.show {
  visibility: visible; /* Show the snackbar */
  /* Add animation: Take 0.5 seconds to fade in and out the snackbar.
  However, delay the fade out process for 2.5 seconds */
  -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
  animation: fadein 0.5s, fadeout 0.5s 2.5s;
}

/* Animations to fade the snackbar in and out */
@-webkit-keyframes fadein {
  from {bottom: 0; opacity: 0;}
  to {bottom: 30px; opacity: 1;}
}

@keyframes fadein {
  from {bottom: 0; opacity: 0;}
  to {bottom: 30px; opacity: 1;}
}

@-webkit-keyframes fadeout {
  from {bottom: 30px; opacity: 1;}
  to {bottom: 0; opacity: 0;}
}

@keyframes fadeout {
  from {bottom: 30px; opacity: 1;}
  to {bottom: 0; opacity: 0;}
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
      <h4 class="card-title ">Show Vendors</h4>
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
                          <label class="bmd-label-floating">Category</label>
                        <select name="cat_id" id="cat_id" class="selectpicker" data-live-search="true">
                        	<option value="">-None-</option>
                        	@foreach($categories as $cat)
                        		<option value="{{$cat->cat_id}}" @if($request->cat_id==$cat->cat_id) selected="" @endif>{{$cat->title}}</option>
                 			@endforeach
                        </select>
                        </div>
                      </div>
                    </div>
                    <button type="submit" class="btn btn-primary pull-center">Submit</button>
    				
    </form>
    <br>
	</div>
</div>

<div class="row">
<div class="col-lg-12">
   
<div class="card">
<div class="card-header card-header-primary">
      <h4 class="card-title ">Vendors</h4>
    </div>
<div class="container" style="overflow-x:scroll"> <br> 

<table class="display datatable table-bordered" id="myTable">
    <thead>
        <tr>
            <th>#</th>
            <th>Vendor Name</th>
        	<th>Position</th>
        </tr>
    </thead>
    <tbody>
          @if(!empty($stores))
          @php $i=1; @endphp
          @foreach($stores as $store)
        <tr>
            <td class="text-center">{{$i}}</td>
        	<td>#{{$store->store_id}} - {{$store->store_name}}</td>
        	<td>
				<form method="post" class="form_priority" action="">
                	{{csrf_field()}}
            		<input type="number" name="store_priority" value="{{$store->store_priority}}"/>
                	<input type="hidden" name="store_id" value="{{$store->store_id}}"/>
                	<input type="hidden" name="cat_id" value="{{$request->cat_id}}"/>
                	
	            	<button type="submit" class="btn btn-sm btn-primary">Change</button>
                	<div class="loader"><div class="indeterminate"></div></div>
            	</form>
        	</td>
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
            <th>Vendor Name</th>
        	<th>Position</th>
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
<div id="snackbar">
	Vendor Priority Changed
</div>
     <script>
       	$(document).ready(function(){
        	$('#myTable').DataTable();
       	});
				$('.form_priority').on('submit',function(e){
					e.preventDefault();
                	var loader = $(this).find('.loader');
					loader.css('opacity',1);
					$.ajax({
						type: "POST",
						url: '{{route('set_store_priority')}}',
						data: new FormData(this),
						dataType:'json',
						contentType: false,
						processData: false,
						headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},
						success: function(data){
                        	if(data.success == true){
                            	$('#snackbar').html("Vendor Priority Changed");
                        		showSnakebar();
                            	
                            }else{
                            	$('#snackbar').html("Some Error Occurred");
                            	showSnakebar();
                            }
                        	loader.css('opacity',0);
					}
					});
        			return false;
				});
// 		});
     	function showSnakebar() {
  			// Get the snackbar DIV
  			var x = document.getElementById("snackbar");
		
  			// Add the "show" class to DIV
  			x.className = "show";

  			// After 3 seconds, remove the show class from DIV
  			setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
		}
    </script>
    @endsection
</div>
