@extends('admin.layout.app')
<style>
  td{
    text-align:center;
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
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-primary">
          <h4 class="card-title">{{$title}}</h4>
        </div>
        <form class="forms-sample" action="{{route('admin_users.update',$user->id)}}" method="post" autocomplete="off">
        @method('PUT')
          {{csrf_field()}}
          <div style="opacity:0;position:absolute">
            <input type="text" name="username">
            <input type="password" name="password">
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label class="bmd-label-floating">User Type</label>
                  <select name="type" class="form-control" required>
                    <option value="admin" @if($user->user_type=='admin') selected @endif>Admin</option>
                    <option value="user" @if($user->user_type=='user') selected @endif>User</option>
                  </select>
                </div>
                <div class="form-group">
                  <label class="bmd-label-floating">Name</label>
                  <input type="text" name="name" class="form-control" value="{{$user->admin_name}}" required>
                </div>
                <div class="form-group">
                  <label class="bmd-label-floating">Email</label>
                  <input type="text" name="admin_email" class="form-control" value="{{$user->admin_email}}" autocomplete="off" required>
                </div>
                <div class="form-group">
                  <label class="bmd-label-floating">Password</label>
                  <input type="password" name="admin_pass" class="form-control" autocomplete="off">
                </div>
                <div class="form-group">
                  <label class="bmd-label-floating">Location</label>
                  <select name="locations[]" class="form-control" multiple>
                    @foreach($cities as $city)
                    <option value="{{$city->city_name}}" @if(!empty($user->locations) && in_array($city->city_name, explode(',',$user->locations))) selected @endif>{{$city->city_name}}</option>
                    @endforeach
                  </select>
                </div>

                <button type="submit" class="btn btn-primary pull-center">Submit</button>
                <a href="{{route('admin_users.index')}}" class="btn">Close</a>
                <div class="clearfix"></div>
              </div>
              <div class="col-sm-8">
                <h4>User Rights</h4>
                <table class="display table table-bordered">
                  <thead>
                    <th>Module Name</th>
                    <th>All</th>
                    <th>View</th>
                    <th>Add</th>
                    <th>Modify</th>
                    <th>Delete</th>
                  </thead>
                  <tbody>
                    @foreach($modules as $module)
                      <tr>
                        <th>{{$module['title']}}</th>
                        <td>
                          <input type="checkbox" class="check_all" name="all_{{$module['module']}}" value="All" @if(!empty($rights) && !empty($rights[''.$module['module']]) && $module['rights'] == implode(',',$rights[''.$module['module']])) checked @endif/>
                        </td>
                        <td>
                        @if(strpos($module['rights'],'View') !== false )
                        <input type="checkbox" class="check" name="{{$module['module']}}[]" value="View" @if(!empty($rights) && !empty($rights[''.$module['module']]) && in_array('View',$rights[''.$module['module']])) checked @endif/>
                        @else
                        -
                        @endif
                        </td>
                        <td>
                        @if(strpos($module['rights'],'Add') !== false )
                        <input type="checkbox" class="check" name="{{$module['module']}}[]" value="Add" @if(!empty($rights) && !empty($rights[''.$module['module']]) && in_array('Add',$rights[''.$module['module']])) checked @endif/>
                        @else
                        -
                        @endif
                        </td>
                        <td>
                        @if(strpos($module['rights'],'Edit') !== false )
                        <input type="checkbox" class="check" name="{{$module['module']}}[]" value="Edit" @if(!empty($rights) && !empty($rights[''.$module['module']]) && in_array('Edit',$rights[''.$module['module']])) checked @endif/>
                        @else
                        -
                        @endif
                        </td>
                        <td>
                        @if(strpos($module['rights'],'Delete') !== false )
                        <input type="checkbox" class="check" name="{{$module['module']}}[]" value="Delete" @if(!empty($rights) && !empty($rights[''.$module['module']]) && in_array('Delete',$rights[''.$module['module']])) checked @endif/>
                        @else
                        -
                        @endif
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
</div>
<script>
function setJsSwicth() {
        let elems = Array.prototype.slice.call(document.querySelectorAll('input[type="checkbox"]'));
        elems.forEach(function(html) {
            let switchery = new Switchery(html, {
                size: 'small'
            });
        });
    }
    function changeSwitchery(elements, checked) {
      let elems = Array.prototype.slice.call(elements);
        elems.forEach(function(ele) {
          if ( ( $(ele).is(':checked') && checked == false ) || ( !$(ele).is(':checked') && checked == true ) ) {
            $(ele).parent().find('.switchery').trigger('click');
          }
        });
    }
  $(document).ready(function() {
    setJsSwicth();
    $('select[multiple]').multiselect({
      columns: 1,
      search: true,
      selectAll: true,
      texts: {
        placeholder: 'Select Locations',
        search: 'Search Location'
      }
    });
    var btnCheckAll = null;
    $('.check_all').change(function(){
      // if($(this).attr('name') == btnCheckAll){
      //   btnCheckAll = null;
      //   return;
      // }
        if($(this).is(':checked')){
          changeSwitchery($(this).parent().parent().find('.check'),true);
        }else{
          changeSwitchery($(this).parent().parent().find('.check'),false);
        }
    });
    $('.check').change(function(){
        // if(!$(this).is(':checked') && $(this).attr('name')!=null){
        //   btnCheckAll = $(this).parent().parent().find('.check_all').attr('name');
        //   changeSwitchery($(this).parent().parent().find('.check_all'),false);
        // }
    });

  });
</script>
@endsection