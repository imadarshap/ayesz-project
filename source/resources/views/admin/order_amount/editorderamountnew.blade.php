@extends('admin.layout.app')

@section ('content')
<style>
    .charges .row{
        border: 1px solid #ccc;
        padding: 20px 20px;
        margin: 5px 0px 10px 0px;
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
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title">Delivery Charges</h4>
                  
                </div>
                <div class="card-body">
                    <form class="forms-sample" action="" method="get" enctype="multipart/form-data">
                      
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating">Category</label>
                          <select name="cat_id" class="form-control" required="">
                              <option value="" disabled="" selected="">Select Category</option>
                              @foreach($categories as $cat)
                              <option value="{{$cat->cat_id}}" <?php if(isset($_GET['cat_id'])&&$_GET['cat_id']==$cat->cat_id)echo 'selected';?>>{{$cat->title}}</option>
        		              @endforeach
                          </select>
                        </div>
                      </div>
                    </div>
                    <button type="submit" class="btn btn-primary pull-center">Get/Set Charges</button>
                    </form>
                    <?php if(isset($_GET['cat_id'])){?>
                    <form class="form_charges" action="{{route('amountupdatenew')}}" method="post" enctype="multipart/form-data" >
                      {{csrf_field()}}  
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating">Charge Type</label>
                          <select name="charge_type" class="form-control" required="">
                              <option value="" disabled="" selected="">Select Charge Type</option>
                              <option value="by_weight" <?php if(!empty($charges)&& $charges->charge_type=='by_weight')echo 'selected';?>>By Weight</option>
                              <option value="by_distance" <?php if(!empty($charges)&&$charges->charge_type=='by_distance')echo 'selected';?>>By Distance</option>
                              <option value="by_cart_price" <?php if(!empty($charges)&&$charges->charge_type=='by_cart_price')echo 'selected';?>>By Cart Price</option>
                          </select>
                        </div>
                      </div>
                    </div>  
                    
                    <label class="mt-2">Charges</label>
                    <div class="charges">
                        <?php 
                        if(!empty($charges)){
                          $min = explode('*', $charges->min);
                          $max = explode('*', $charges->max); 
                          $charge = explode('*', $charges->charge);
                          $count = 0;
                          foreach($min as $minval){
                              $count++;
                          
                        ?>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="bmd-label-floating">Min Value (Gms/Km/Rs)</label>
                                    <input type="number" name="min[]" value="<?=$min[$count-1]?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="bmd-label-floating">Max Value (Gms/Km/Rs)</label>
                                    <input type="number" name="max[]" value="<?=$max[$count-1]?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="bmd-label-floating">Charges</label>
                                    <input type="number" name="charge[]" value="<?=$charge[$count-1]?>" class="form-control" required>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-danger btn_remove_option float-right">Remove</button>
                        </div>
                        <?php }}else{?>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="bmd-label-floating">Min Value (Gms/Km/Rs)</label>
                                    <input type="number" name="min[]" value="" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="bmd-label-floating">Max Value (Gms/Km/Rs)</label>
                                    <input type="number" name="max[]" value="" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="bmd-label-floating">Charges</label>
                                    <input type="number" name="charge[]" value="" class="form-control" required>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-danger btn_remove_option float-right">Remove</button>
                        </div>
                        <?php }?>
                    </div>
                    <button type="button" id="btn_add_option" class="btn btn-sm btn-success">Add Option</button>
                    <br/>
                    <input type="hidden" name="cat_id" value="<?=$_GET['cat_id'];?>"/>
                    <button type="submit" class="btn btn-primary pull-center">Submit</button>
                    <div class="clearfix"></div>
                  </form>
                  <?php }?>
                </div>
              </div>
            </div>
			</div>
          </div>
          <script>
              $(document).ready(function(){
                  initRemove();
                  $('#btn_add_option').click(function(){
                      var option = '<div class="row">'
                            +'<div class="col-md-4">'
                                +'<div class="form-group">'
                                    +'<label class="bmd-label-floating">Min Value (Gms/Km/Rs)</label>'
                                    +'<input type="number" name="min[]" value="" class="form-control" required>'
                                +'</div>'
                            +'</div>'
                            +'<div class="col-md-4">'
                                +'<div class="form-group">'
                                +'    <label class="bmd-label-floating">Max Value (Gms/Km/Rs)</label>'
                                +'    <input type="number" name="max[]" value="" class="form-control" required>'
                                +'</div>'
                            +'</div>'
                            +'<div class="col-md-4">'
                                +'<div class="form-group">'
                                    +'<label class="bmd-label-floating">Charges</label>'
                                    +'<input type="number" name="charge[]" value="" class="form-control" required>'
                                +'</div>'
                            +'</div>'
                            +'<button type="button" class="btn btn-sm btn-danger btn_remove_option float-right">Remove</button>'
                        +'</div>';
                        $('.charges').append(option);
                        initRemove();
                  });
              });
              function initRemove(){
                  $('.btn_remove_option').click(function(){
                      $(this).parent('.row').remove();
                  });
              }
          </script>
@endsection