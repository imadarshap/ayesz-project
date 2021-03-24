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
                  <h4 class="card-title">Edit Product</h4>
                  <form class="forms-sample" action="{{route('UpdateProduct', $product->product_id)}}" method="post" enctype="multipart/form-data">
                      {{csrf_field()}}
                </div>
                <div class="card-body">
                     <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating">Product_name</label>
                          <input type="text" value="{{$product -> product_name}}" name="product_name" class="form-control">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating">Quantity</label>
                          <input type="number" name="quantity" step="0.01" value="{{$product->quantity}}" class="form-control">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating">Unit (G/KG/Ltrs/Ml)</label>
                          <input type="text" name="unit" value="{{$product->unit}}" class="form-control">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating">Weight in Grams (For Delivery Charges)</label>
                          <input type="number" name="weight" step="0.01" value="{{$product->weight}}" class="form-control">
                        </div>
                      </div>
                    </div>
                	<div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating">Description</label>
                          <textarea type="text" name="description" class="form-control" >{{$product->description}}</textarea>
                        </div>
                      </div>
                    </div>
                    <img src="{{url($product->product_image)}}" alt="image" name="old_image" style="width:100px;height:100px; border-radius:50%">
                     <div class="row">
                      <div class="col-md-6">
                        <div class="form">
                          <label class="bmd-label-floating">Product Image</label>
                          <input type="file"name="product_image" class="form-control">
                        </div>
                      </div>
                    </div>
                	
                	<div class="row">
                      <div class="col-md-6">
                        <div class="form">
                          <label class="bmd-label-floating">Vendors</label>
                        <select name="stores[]" class="form-control" multiple>
                        	@foreach($stores as $store)
        		          	<option value="{{$store->store_id}}" @if($store->selected)selected @endif>{{$store->store_name}}({{$store->employee_name}}) - {{$store->city}}</option>
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




