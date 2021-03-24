@extends('store.layout.app')
<link rel="stylesheet" href="{{url('assets/fsselect/fstdropdown.css')}}">
<style>
.selecteeee {
    width: 100%;
    height: 450px;
}
.fstdropdown > .fstlist {
    display: none;
    max-height: 430px !important;
    overflow-y: auto;
    overflow-x: hidden;
}
.table .td-actions .btn {
    margin: 0px;
    height: 25px;
    padding: 25px 8px 11px 8px !important;
}
.loader {
  margin:0 auto;
  margin-bottom:20px;
  border: 16px solid #f3f3f3; /* Light grey */
  border-top: 16px solid #3498db; /* Blue */
  border-radius: 50%;
  width: 120px;
  height: 120px;
  animation: spin 2s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
#product_detail{
	display:none;
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
            <div class="col-md-5">
              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title">Select Products</h4>
                  
         <form class="forms-sample" action="{{route('added_product')}}" method="post" enctype="multipart/form-data">
                      {{csrf_field()}}
                </div>
                <div class="card-body">

                    <div class="row">
                      <div class="col-md-12">
                        <div class="form-group">
                          <label class="bmd-label-floating">Select Products You Have</label><br>
                        <select class='fstdropdown-select pId' style="max-height: 500px;" id="eightieth" data-opened="true" name="prod[]">
                          <option value="" disabled>Choose your Product</option>
                          @foreach($products as $product)
                          <option value="{{$product->varient_id}}">{{$product->product_name}}({{$product->quantity}}{{$product->unit}})</option>
                          @endforeach
                        </select>

                        </div>
                      </div>

                    </div>
					<button type="button" class="btn btn-primary pull-center btn-open-modal">Submit</button>
                    <div class="clearfix"></div>
                  </form>
                </div>
              </div>
            </div>
             <div class="col-md-7">
              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title">Selected Products</h4>
                 </div>
                     <table class="table">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th style="width:33.33%">Product Name</th>
                                <th style="width:33.33%">Categories</th>
                                <th style="width:33.33%">price</th>
                                <th style="width:33.33%">MRP</th>
                                <th class="text-center" style="width:33.33%">Current Stock</th>
                                <th class="text-right" style="width:33.33%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                             
                              @if(count($selected)>0)
                      @php $i=1; @endphp
                      @foreach($selected as $sel)
                    <tr>
                        <td class="text-center">{{$i}}</td>
                        <td><p>{{$sel->product_name}}({{$sel->quantity}} {{$sel->unit}})</p></td>
                        <td><p>{{$sel->title}}</p></td>
                        <td><p>{{$sel->price}}</p></td>
                        <td><p>{{$sel->mrp}}</p></td>
                        <td align="center">{{$sel->stock}}</td>
                        <td class="td-actions text-right">
                           <a href="{{route('delete_product', $sel->p_id)}}" rel="tooltip" class="btn btn-danger">
                                <i class="material-icons">close</i>
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
                    <div class="pagination justify-content-end" align="right" style="width:100%;float:right !important">{{$selected->links()}}</div>
                </div>
              </div>
            </div>
			</div>
          </div>      

<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add Product</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
    <div class="modal-body loader-body">
      <div class="loader"></div>
    </div>
    <div id="product_detail">
    <form class="forms-sample" action="{{route('added_product')}}" method="post" enctype="multipart/form-data">
                      {{csrf_field()}}
      <div class="modal-body">
        <table class="table table-stripped" style="width:100%">
        	<tr>
            	<th>Product Image</th>
            	<td><img width="100" id="pImg" src=""/></td>
        	</tr>
        	<tr>
            	<th>Product Name</th>
            	<td id="pName"></td>
        	</tr>
        	<tr>
            	<th>Main Category</th>
            	<td id="pCat"></td>
        	</tr>
        	<tr>
            	<th>Sub Category</th>
            	<td id="pSubCat"></td>
        	</tr>
        	<tr>
            	<th>Quantity</th>
            	<td id="pQty"></td>
        	</tr>
        	<tr>
            	<th>Unit (G/KG/Ltrs/Ml)</th>
            	<td id="pUnit"></td>
        	</tr>
        	<tr>
            	<th>MRP</th>
            	<td><input type="number" id="pMrp" step="0.01" min="1" name="mrp" class="form-control" required="" autocomplete="off"></td>
        	</tr>
        	<tr>
            	<th>Price</th>
            	<td><input type="number" id="pPrice" step="0.01" min="1" name="price" class="form-control" required="" autocomplete="off"></td>
        	</tr>
        	<tr>
            	<th>Stock</th>
            	<td><input type="number" id="pStock" step="1" min="1" name="stock" class="form-control" required="" autocomplete="off"></td>
        	</tr>
        	
      	</table>
      </div>
      <div class="modal-footer">
      	<input type="hidden" id="product_id" name="product_id" value=""/> 
      	<button type="submit" class="btn btn-primary">Add Product</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </form>
	</div>
    </div>

  </div>
</div>
<script>
$(document).ready(function(){
	$('.btn-open-modal').click(function(){
    	$('#product_detail').hide();
    	$('.loader-body').show();
    	$('#myModal').modal().show();
    	var pId = $('.pId').val();
    	$.post("<?=$app->make('url')->to('/api/store/get_product_detail');?>",
  		{
    		product_id: pId
  		},
  		function(data, status){
        	console.log(data);
        	$('#product_id').val(data.data.varient.varient_id);
        	$('#pImg').attr('src',"<?=$app->make('url')->to('/')?>"+"/"+data.data.product_image);
        	$('#pName').html(data.data.product_name);
        	$('#pCat').html(data.data.m_cat);
        	$('#pSubCat').html(data.data.s_cat);
        	$('#pQty').html(data.data.varient.quantity);
        	$('#pUnit').html(data.data.varient.unit);
        	$('#pMrp').val(data.data.varient.base_mrp);
        	$('#pPrice').val(data.data.varient.base_price);
        	$('#product_detail').show();
    		$('.loader-body').hide();
  		});
	});
});
</script>
@endsection
<script src="{{url('assets/fsselect/fstdropdown.js')}}"></script>
    <script>
        function setDrop() {
            if (!document.getElementById('third').classList.contains("fstdropdown-select"))
                document.getElementById('third').className = 'fstdropdown-select';
            setFstDropdown();
        }
        setFstDropdown();
        function removeDrop() {
            if (document.getElementById('third').classList.contains("fstdropdown-select")) {
                document.getElementById('third').classList.remove('fstdropdown-select');
                document.getElementById("third").fstdropdown.dd.remove();
            }
        }
        function addOptions(add) {
            var select = document.getElementById("fourth");
            for (var i = 0; i < add; i++) {
                var opt = document.createElement("option");
                var o = Array.from(document.getElementById("fourth").querySelectorAll("option")).slice(-1)[0];
                var last = o == undefined ? 1 : Number(o.value) + 1;
                opt.text = opt.value = last;
                select.add(opt);
            }
        }
        function removeOptions(remove) {
            for (var i = 0; i < remove; i++) {
                var last = Array.from(document.getElementById("fourth").querySelectorAll("option")).slice(-1)[0];
                if (last == undefined)
                    break;
                Array.from(document.getElementById("fourth").querySelectorAll("option")).slice(-1)[0].remove();
            }
        }
        function updateDrop() {
            document.getElementById("fourth").fstdropdown.rebind();
        }
    	
    </script>



