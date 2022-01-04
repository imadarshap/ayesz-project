@extends('store.layout.app')
<link rel="stylesheet" href="{{url('assets/fsselect/fstdropdown.css')}}">

@section ('content')
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
label {
    font-size: 16px;
    line-height: 1.42857;
    color: #000;
    font-weight: 600;
}
.divider{
    padding: 10px;
    font-size: 28px;
}
.btn-link{
    border: none !important;
    padding: 0 !important;
    display:none;
}
.hours{
    display:none;
}
.day label{
    width:120px;
}
.btn-outline-danger{
    width: 40px !important;
    height: 40px !important;
    padding: 0px !important;
    border: none !important;
    box-shadow: none !important;
}
.btn-outline-danger i{
    margin:0px !important;
    font-size:24px !important;
}
@if(!empty($mobile))

.content{
    margin:0px !important;
    padding:0px !important;
}
.content .container-fluid{
    padding:0px !important;
}
.card{
    margin: 0 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
}
.card-header{
    display:none !important;
}
.card-body{
    height: 100vh !important;
}
.btn-primary{
    background: #4CAF50 !important;
    width: 100% !important;
    font-size:16px !important;
}

.alert{
    margin-bottom:0pc !important;
}
@endif
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
                  <h4 class="card-title">Availability</h4>
                </div>
                <div class="card-body">
                    <form class="forms-sample" action="{{route('set_availability')}}" method="post" enctype="multipart/form-data">
                        {{csrf_field()}}
                        <div class="col-md-12">
                            @php
                                $hours = ['00:00','00:30','01:00','01:30','02:00','02:30','03:00','03:30','04:00','04:30','05:00','05:30','06:00','06:30','07:00','07:30','08:00','08:30','09:00','09:30','10:00','10:30','11:00','11:30','12:00','12:30',
                                        '13:00','13:30','14:00','14:30','15:00','15:30','16:00','16:30','17:00','17:30','18:00','18:30','19:00','19:30','20:00','20:30','21:00','21:30','22:00','22:30','23:00','23:30'];
                                $day_names = ['Monday','Tuesday','Wedneuday','Thursday','Friday','Saturday','Sunday'];
                            @endphp
                            
                            @for($i=0;$i < count($day_names);$i++)
                            @php
                                $day_name = $day_names[$i];
                                $day = strtolower(substr($day_name,0,3));
                            @endphp
                            
                            <div class="day">
                                <label>{{$day_name}} &nbsp;</label>
                                <input align="center" type="checkbox" name="{{$day}}_status" value="1" class="js-switch" data-switchery="true" @if(count($avails)>0 && $avails[$i]->status == 1) checked @endif>
                                <div class="hours" @if(count($avails)>0 && $avails[$i]->status == 1) style="display:block" @endif>
                                    @if(count($avails)>0 && trim($avails[$i]->start_time) !='')
                                    @php
                                        $start_time = explode(',',$avails[$i]->start_time);
                                        $end_time = explode(',',$avails[$i]->end_time);
                                    @endphp
                                    @for($j=0;$j < count($start_time);$j++)
                                    <div class="row">
                                        <div class="col-4 col-md-3">
                                            <div class="form-group">
                                                <select class="form-control start_time" name="{{$day}}_start[]" onchange="changeHour(this,1)" required>
                                                    <option value=""></option>
                                                    <option value="24" @if($start_time[$j]=='24') selected @endif>24 Hours</option>
                                                    @foreach($hours as $hour)
                                                    <option value="{{$hour}}" @if($start_time[$j]==$hour) selected @endif>{{$hour}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-1 col-md-1 divider">-</div>
                                        <div class="col-4 col-md-3" >
                                            <div class="form-group">
                                                <select class="form-control end_time" name="{{$day}}_end[]" onchange="changeHour(this,2)" @if($start_time[$j]=='24') style="display:none" @else required @endif >
                                                    <option value=""></option>
                                                    <option value="24">24 Hours</option>
                                                    @foreach($hours as $hour)
                                                    <option value="{{$hour}}" @if($start_time[$j]!='24' && $end_time[$j]==$hour) selected @endif>{{$hour}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        @if($j>0)
                                        <div class="col-2 col-md-2">
                                            <button type="button" class="btn btn-outline-danger btn-sm btn-close" onclick="removeHour(this);"><i class="material-icons">close</i></button>
                                        </div>
                                        @endif
                                    </div>
                                    @endfor
                                    @else
                                    <div class="row">
                                        <div class="col-4 col-md-3">
                                            <div class="form-group">
                                                <select class="form-control start_time" name="{{$day}}_start[]" onchange="changeHour(this,2)">
                                                    <option value=""></option>  
                                                    <option value="24">24 Hours</option>
                                                    @foreach($hours as $hour)
                                                    <option value="{{$hour}}">{{$hour}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-1 col-md-1 divider">-</div>
                                        <div class="col-4 col-md-3">
                                            <div class="form-group">
                                                <select class="form-control end_time" name="{{$day}}_end[]" onchange="changeHour(this,2)">
                                                    <option value=""></option>
                                                    <option value="24">24 Hours</option>
                                                    @foreach($hours as $hour)
                                                    <option value="{{$hour}}">{{$hour}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <button type="button" class="btn btn-outline-info btn-sm btn-link" onclick="addHours('{{$day}}',this);" @if(count($avails)>0 && $avails[$i]->status == 1 && $avails[$i]->start_time != '24') style="display:block" @endif >Add Hours</button>
                            </div>
                            @endfor
                        </div>
                        <button type="submit" class="btn btn-primary pull-center mt-3">Submit</button>
                    <div class="clearfix"></div>
                  </form>
                </div>
              </div>
            </div>
			</div>
          </div>   
          <script>
            function addHours(day,ele){
                var html = '<div class="row">'
                                        +'<div class="col-4 col-md-3">'
                                            +'<div class="form-group">'
                                                +'<select class="form-control start_time"  name="'+day+'_start[]" onchange="changeHour(this,1)" required>'
                                                    +'<option value=""></option>'  
                                                    +'<option value="24">24 Hours</option>'
                                                    +'@foreach($hours as $hour)'
                                                    +'<option value="{{$hour}}">{{$hour}}</option>'
                                                    +'@endforeach'
                                                +'</select>'
                                            +'</div>'
                                        +'</div>'
                                        +'<div class="col-1 col-md-1 divider">-</div>'
                                        +'<div class="col-4 col-md-3">'
                                            +'<div class="form-group">'
                                                +'<select class="form-control end_time" name="'+day+'_end[]" onchange="changeHour(this,2)" required>'
                                                    +'<option value=""></option>'
                                                    +'<option value="24">24 Hours</option>'
                                                    +'@foreach($hours as $hour)'
                                                    +'<option value="{{$hour}}">{{$hour}}</option>'
                                                    +'@endforeach'
                                                +'</select>'
                                            +'</div>'
                                        +'</div>'
                                        +'<div class="col-2 col-md-2">'
                                            +'<button type="button" class="btn btn-outline-danger btn-sm btn-close" onclick="removeHour(this);"><i class="material-icons">close</i></button>'
                                        +'</div>'
                                    +'</div>';
                $(ele).parent().find('.hours').append(html);
            }
            function removeHour(ele){
                $(ele).parent().parent().remove();
            }
            function changeHour(ele,input){
                if($(ele).val()==24){
                    $(ele).parent().parent().parent().find('.end_time').hide();
                    $(ele).parent().parent().parent().find('.end_time').removeAttr('required');
                    $(ele).parent().parent().parent().parent().parent().find('.btn-link').hide();
                    if(input==2){
                        $(ele).parent().parent().parent().find('.start_time').val('24');
                    }
                }else{
                    $(ele).parent().parent().parent().find('.end_time').show();
                    $(ele).parent().parent().parent().find('.end_time').prop('required','true');
                    $(ele).parent().parent().parent().parent().parent().find('.btn-link').show();
                }
            }
            $(document).ready( function () {
                $('.js-switch').change(function(){
                    if($(this).is(":checked")){
                        $(this).parent().find('.hours').show();
                        $(this).parent().find('.btn-link').show();
                        $(this).parent().find('.form-control').prop('required','true');
                    }else{
                        $(this).parent().find('.hours').hide();
                        $(this).parent().find('.btn-link').hide();
                        $(this).parent().find('.form-control').removeAttr('required');
                    }
                });
            } );
          </script>
@endsection


