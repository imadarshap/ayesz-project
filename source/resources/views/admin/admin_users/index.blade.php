@extends('admin.layout.app')
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
<style>
    .material-icons {
        margin-top: 0px !important;
        margin-bottom: 0px !important;
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
        <div class="col-lg-12">  
            <a href="{{route('admin_users.create')}}" class="btn btn-primary ml-auto" style="width:15%;float:right;padding: 3px 0px 3px 0px;"><i class="material-icons">add</i>Add Admin User</a>
        </div> 
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title ">{{$title}}</h4>
                </div>
                <div class="container"> <br>
                    <table class="display table" id="myTable" data-ordering="false">
                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                    <tbody>
                        @php $count=0; @endphp
                        @foreach($admins as $admin)
                        @php $count++; @endphp
                        <tr>
                            <td>{{$count}}</td>
                            <td>{{$admin->admin_name}}</td>
                            <td>{{$admin->admin_email}}</td>
                            <td>{{$admin->user_type}}</td>
                            <td>
                            <input type="checkbox" data-id="{{$admin->id}}" name="status" class="js-switch" @if($admin->status==1) checked @endif/>
                            </td>
                            <td>
                                <a href="{{route('admin_users.edit',$admin->id)}}" class="btn btn-info btn-sm"><i class="material-icons">edit</i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function setJsSwicth() {
        let elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        elems.forEach(function(html) {
            let switchery = new Switchery(html, {
                size: 'small'
            });
        });
        $('.js-switch').change(function() {
            let status = $(this).prop('checked') === true ? 1 : 0;
            let admin_id = $(this).data('id');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: "{{url('admin_users')}}/"+admin_id+"/status/"+status,
                success: function(data) {
                    console.log(data);
                }
            });
        });
    }
    $(document).ready(function() {
        $('#myTable').DataTable();
        setJsSwicth();
    });
</script>
@endsection
</div>