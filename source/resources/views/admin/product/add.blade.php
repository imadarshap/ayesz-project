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
                  <h4 class="card-title">Add Product</h4>
                  <form class="forms-sample" action="{{route('AddNewProduct')}}" method="post" enctype="multipart/form-data">
                      {{csrf_field()}}
                </div>
                <div class="card-body">

                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating">Category</label>
                          <select name="cat_id" class="form-control" required>
                              <option value="" disabled selected>Select Category</option>
                              @foreach($category as $categorys)
                              
        		          	<option value="{{$categorys->cat_id}}">{{$categorys->title}}</option>
        		              @endforeach
                              
                          </select>
                        </div>
                      </div>

                    </div>

 
                     <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating">Product Name</label>
                          <input type="text" name="product_name" class="form-control" required>
                        </div>
                      </div>

                    </div>
                     <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating">Quantity</label>
                          <input type="number" name="quantity" class="form-control" required>
                        </div>
                      </div>
                    </div>
                    
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating">Unit (G/KG/Ltrs/Ml)</label>
                          <input type="text" name="unit" class="form-control" title="KG/G/Ltrs/Ml etc" required>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating">Weight in Grams (For Delivery Charges)</label>
                          <input type="number" name="weight" step="0.01" class="form-control">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating">MRP</label>
                          <input type="number" step="0.01" name="mrp" class="form-control" required>
                        </div>
                      </div>
                    </div>
                    <div class="row" style="display:none">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating">Price</label>
                          <input type="number" step="0.01" name="price" class="form-control">
                        </div>
                      </div>
                    </div>
                    
                     <div class="row">
                      <div class="col-md-6">
                        <div class="form">
                          <label class="bmd-label-floating">Product Image</label>
                          <input type="file"name="product_image" class="form-control" required>
                        </div>
                      </div>
                    </div>
                    
                     <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating">Description</label>
                          <textarea type="text" name="description" class="form-control" ></textarea>
                        </div>
                      </div>
                    </div>
                	<!--<div class="row">
                		<div class="col-md-6">
                        <div class="form">
                           <label class="bmd-label-floating"> Select City</label>
                          <select name="city" class="form-control">
                              <option disabled selected>Select City</option>
                              @foreach($city as $cities)
                              <option value="{{$cities->city_name}}">{{$cities->city_name}}</option>
                              @endforeach
                         </select>
                        </div>
                      </div>
                	</div>-->
                	<div class="row">
                      <div class="col-md-6">
                        <div class="form">
                          <label class="bmd-label-floating">Vendors</label>
                        <select name="stores[]" class="form-control" multiple>
                        	@foreach($stores as $store)
        		          	<option value="{{$store->store_id}}">{{$store->store_name}}({{$store->employee_name}}) - {{$store->city}}</option>
        		              @endforeach
                        </select>
                        </div>
                      </div>
                    </div>


                    <button type="submit" class="btn btn-primary pull-center">Submit</button>
                    <a href="{{route('productlist')}}" class="btn">Close</a>
                    <div class="clearfix"></div>
                  </form>
                </div>
              </div>
            </div>
			</div>
          </div>
<script>
    $(document).ready(function(){
    	$('select[multiple]').multiselect({
    		columns  : 1,
    		search   : true,
    		selectAll: true,
    		texts    : {
        		placeholder: 'Select Vendors',
        		search     : 'Search Vendors'
    		}
	});
    
    });
</script>
@endsection




