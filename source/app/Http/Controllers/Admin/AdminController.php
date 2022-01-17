<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\UserRight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = "Admin Panel Users";
        $admin_email = Session::get('bamaAdmin');
		$admin = DB::table('admin')
			->where('admin_email', $admin_email)
			->first();
		if (!Helper::hasRight($admin->id, 'admin_users', 'View')) {
			return abort(403);
		}
        $logo = DB::table('tbl_web_setting')
            ->where('set_id', '1')
            ->first();
        $admins = Admin::get();

        return view('admin.admin_users.index', compact('title', 'logo', 'admin', 'request','admins'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = "Create User";
        $admin_email = Session::get('bamaAdmin');
		$admin = DB::table('admin')
			->where('admin_email', $admin_email)
			->first();
		if (!Helper::hasRight($admin->id, 'admin_users', 'Add')) {
			return abort(403);
		}
        $logo = DB::table('tbl_web_setting')
            ->where('set_id', '1')
            ->first();
        $cities = DB::table('city')->get();
        $modules = Helper::getModules();

        return view('admin.admin_users.add', compact('title', 'logo', 'admin', 'cities', 'modules'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $admin_email = Session::get('bamaAdmin');
		$admin = DB::table('admin')
			->where('admin_email', $admin_email)
			->first();
		if (!Helper::hasRight($admin->id, 'admin_users', 'Add')) {
			return abort(403);
		}
        $this->validate(request(), [
            'name' => 'required',
            'admin_email' => 'required',
            'admin_pass' => 'required',
            'type' => 'required',
        ]);

        $admin = new Admin();
        $admin->admin_name = $request->name;
        $admin->admin_email = $request->admin_email;
        $admin->admin_pass = Hash::make($request->admin_pass);
        $admin->user_type = $request->type;
        if(!empty($request->locations))
            $admin->locations = implode(",",$request->locations);
        $admin->status = 1;

        if($admin->save()){
            $modules = Helper::getModules();
            foreach($modules as $module){
                if(!empty($request->get(''.$module['module']))){
                    $right = new UserRight();
                    $right->admin_id = $admin->id;
                    $right->module = $module['module'];
                    $right->rights = implode(",",$request->get(''.$module['module']));
                    $right->save();
                }
            }
            return redirect()->route('admin_users.create')->with('success','User Added Successfully');
        }
        else{
            return redirect()->route('admin_users.create')->withErrors("Something Wents Wrong");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function show(Admin $admin)
    {
        $admin_email = Session::get('bamaAdmin');
		$admin = DB::table('admin')
			->where('admin_email', $admin_email)
			->first();
		if (!Helper::hasRight($admin->id, 'admin_users', 'View')) {
			return abort(403);
		}
        $pageName = "Admin Details - ".$admin->name;
        return view('admin.admin.show', compact('pageName','admin'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $title = "Edit Admin User";
        $admin_email = Session::get('bamaAdmin');
		$admin = DB::table('admin')
			->where('admin_email', $admin_email)
			->first();
		if (!Helper::hasRight($admin->id, 'admin_users', 'Edit')) {
			return abort(403);
		}
        $logo = DB::table('tbl_web_setting')
            ->where('set_id', '1')
            ->first();
        $cities = DB::table('city')->get();
        $modules = Helper::getModules();
        
        $user = Admin::find($id);
        $user_rights = UserRight::where('admin_id',$id)->get();
        $rights = array();
        foreach($user_rights as $right){
            $rights[''.$right->module] = explode(',',$right->rights);
        }
        // echo json_encode($rights);die;

        return view('admin.admin_users.edit', compact('title', 'logo', 'admin', 'cities', 'modules', 'user','rights'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $admin_email = Session::get('bamaAdmin');
		$admin = DB::table('admin')
			->where('admin_email', $admin_email)
			->first();
		if (!Helper::hasRight($admin->id, 'admin_users', 'Edit')) {
			return abort(403);
		}
        $this->validate(request(), [
            'name' => 'required',
            'admin_email' => 'required',
            'type' => 'required',
        ]);

        $admin = Admin::find($id);
        $admin->admin_name = $request->name;
        $admin->admin_email = $request->admin_email;
        $admin->user_type = $request->type;
        if(!empty($request->locations))
            $admin->locations = implode(",",$request->locations);
        if(!empty($request->admin_pass)){
            $admin->admin_pass = Hash::make($request->admin_pass);
        }

        if($admin->save()){
            $modules = Helper::getModules();
            foreach($modules as $module){
                if(!empty($request->get(''.$module['module']))){
                    $right = UserRight::where('admin_id',$id)->where('module',$module['module'])->first();
                    if(empty($right))
                        $right = new UserRight();
                    $right->admin_id = $admin->id;
                    $right->module = $module['module'];
                    $right->rights =  implode(",",$request->get(''.$module['module']));
                    $right->save();
                }else{
                    UserRight::where('admin_id',$id)->where('module',$module['module'])->delete();
                }
            }
            return redirect()->route('admin_users.edit',$id)->with('success','User Updated Successfully');
        }
        else{
            return redirect()->route('admin_users.edit',$id)->withErrors("Something Wents Wrong");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function destroy(Admin $admin)
    {
        $admin_email = Session::get('bamaAdmin');
		$admin = DB::table('admin')
			->where('admin_email', $admin_email)
			->first();
		if (!Helper::hasRight($admin->id, 'admin_users', 'Delete')) {
			return abort(403);
		}
        if(empty($admin))
            return redirect()->route('admin_users.index')->withErrors("Invalid Admin - Not Found!");
        $admin->status = -1;
        if($admin->save()){
            return redirect()->route('admin_users.index')->with('success','Admin Deleted Successfully');
        }
        else{
            return redirect()->route('admin_users.create')->withErrors("Something Wents Wrong");
        }
    }

    public function status($id,$status){
        $admin_email = Session::get('bamaAdmin');
		$admin = DB::table('admin')
			->where('admin_email', $admin_email)
			->first();
		if (!Helper::hasRight($admin->id, 'admin_users', 'Edit')) {
			return abort(403);
		}
        $admin = Admin::find($id);
        if(empty($admin))
            return array("message"=>"Invalid Admin - Not Found!");
        $admin->status = $status;
        if($admin->save()){
            return array("message"=>'Admin Status Updated Successfully');
        }
        else{
            return array("message"=>"Something Wents Wrong");
        }
    }
}
