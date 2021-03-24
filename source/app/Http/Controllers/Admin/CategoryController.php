<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Carbon\carbon;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function list(Request $request)
    {
        $title = "Category List";
         $admin_email=Session::get('bamaAdmin');
    	 $admin= DB::table('admin')
    	 		   ->where('admin_email',$admin_email)
    	 		   ->first();
    	  $logo = DB::table('tbl_web_setting')
                ->where('set_id', '1')
                ->first();
                
           $category = DB::table('categories')
                    ->leftJoin('categories as catt', 'categories.parent', '=' , 'catt.cat_id')
                    ->select('categories.*', 'catt.title as tttt')
                    ->get();
                    
          $adminTopApp =  DB::table('categories')
                  ->get();
   
    return view('admin.category.index', compact('title',"admin", "logo","category","adminTopApp"));
    }

    
     public function AddCategory(Request $request)
    {
    
        $title = "Add Category";
         $admin_email=Session::get('bamaAdmin');
    	 $admin= DB::table('admin')
    	 		   ->where('admin_email',$admin_email)
    	 		   ->first();
    	  $logo = DB::table('tbl_web_setting')
                ->where('set_id', '1')
                ->first();
           $category = DB::table('categories')
                    ->where('level', 0)
                    // ->orWhere('level', 1)
                    ->get();
           $stores= DB::table('store')->get();
        
        return view('admin.category.add',compact("category", "admin_email","logo", "admin","title","stores"));
     }
    
     public function AddNewCategory(Request $request)
    {
        $parent_id = $request->parent_id;
        $category_name = $request->cat_name;
        $status = 1;
        $slug = str_replace(" ", '-', $category_name);
        $date=date('d-m-Y');
        $desc = $request->desc;
          
        if($desc==NULL){
          $desc= $category_name; 
        }
        $category = DB::table('categories')
                  ->where('cat_id', $parent_id)
                  ->first();
    			         
        if($status=="")
        {
            $status=0;
        }
  
    if($category)
        {    
        if($parent_id==$category->cat_id)
            {
                if($category->level==0){
                    $level = 1;
                } 
                elseif($category->level==1){
                    $level = 2;
                }
            }
        }
        else{
           $level = 0; 
        }
        
     
        $this->validate(
            $request,
                [
                    
                    'cat_name' => 'required',
                    'cat_image' => 'required|mimes:jpeg,png,jpg|max:400',
                ],
                [
                    'cat_name.required' => 'Enter category name.',
                    'cat_image.required' => 'Choose category image.',
                ]
        );

        

        

        if($request->hasFile('cat_image')){
            $category_image = $request->cat_image;
            $fileName = $category_image->getClientOriginalName();
            $fileName = str_replace(" ", "-", $fileName);
            $category_image->move('images/category/'.$date.'/', $fileName);
            $category_image = 'images/category/'.$date.'/'.$fileName;
        }
        else{
            $category_image = 'N/A';
        }

        $insertCategory = DB::table('categories')
                            ->insertGetId([
                                'parent'=>$parent_id,
                                'title'=>$category_name,
                                'slug'=>$slug,
                                'level'=>$level,
                                'image'=>$category_image,
                                'status'=>$status,
                                'description'=>$desc,
                               
                               
                            ]);
        
        if($insertCategory){
        	$stores = $request->stores;
        	if(!empty($stores)){
	            foreach($stores as $store_id){
	                $store_cat = DB::table('store_categories')
	                    ->where('cat_id', $insertCategory)
	                    ->where('store_id',$store_id)
	                    ->first();
	
	                if(!$store_cat){
	                    $save_cat = DB::table('store_categories')
	                            ->insertGetId([
	                                'cat_id'=>$insertCategory,
	                                'store_id'=>$store_id,
	                                'created_at'=>date('Y-m-d'),
	                            ]);
	                }
	            }
	        }
            return redirect()->back()->withSuccess('Category Added Successfully');
        }
        else{
            return redirect()->back()->withErrors("Something Wents Wrong");
        }
      
    }
    
    public function EditCategory(Request $request)
    {
         $category_id = $request->category_id;
         $title = "Edit Category";
         $admin_email=Session::get('bamaAdmin');
    	 $admin= DB::table('admin')
    	 		   ->where('admin_email',$admin_email)
    	 		   ->first();
    	  $logo = DB::table('tbl_web_setting')
                ->where('set_id', '1')
                ->first();
          $category = DB::table('categories')
                    ->where('level', 0)
                    ->orWhere('level', 1)
                    ->where('cat_id','!=',$category_id)
                    ->get();
                    
        $cat=  DB::table('categories')
            ->where('cat_id', $category_id)
            ->first();
    
    	$stores= DB::table('store')->get();
    
    	$store_cats = DB::table('store_categories')->where('cat_id',$category_id)->get();
    
    	$stores = DB::table('store')
					->select('store.store_id','store.store_name','store.employee_name','store.city',DB::Raw('IFNULL(`store_categories`.`cat_id`,0) as selected'))
					->leftJoin('store_categories',function($join)use ($category_id){
                    $join->on('store_categories.store_id','=','store.store_id')
                    ->where('cat_id',$category_id);
                    })
        			
					->get();

        return view('admin.category.edit',compact("category","admin_email","admin","logo","cat","title","stores","store_cats"));
    }

    public function UpdateCategory(Request $request)
    {
        $category_id = $request->category_id;
        $parent_id = $request->parent_id;
        $category_name = $request->cat_name;
        $status = 1;
        $slug = str_replace(" ", '-', $category_name);
        $date=date('d-m-Y');
          $desc = $request->desc;
        if($desc==NULL){
          $desc= $category_name; 
        }
        $category = DB::table('categories')
                  ->where('cat_id', $parent_id)
                  ->first();
    			         
        if($status=="")
        {
            $status=0;
        }
  
    if($category)
        {    
        if($parent_id==$category->cat_id)
            {
                if($category->level==0){
                    $level = 1;
                } 
                elseif($category->level==1){
                    $level = 2;
                }
            }
        }
        else{
           $level = 0; 
        }
        
    	$validator = Validator::make($request->all(), 
                [   
                    'cat_name' => 'required',
                ],
                [
                    'cat_name.required' => 'Enter category name.',
                ]);
        
        $this->validate(
            $request,
                [   
                    'cat_name' => 'required',
                ],
                [
                    'cat_name.required' => 'Enter category name.',
                ]
        );

       $getCategory = DB::table('categories')
                    ->where('cat_id',$category_id)
                    ->first();

        $image = $getCategory->image;

        if($request->hasFile('cat_image')){
            $category_image = $request->cat_image;
            $fileName = $category_image->getClientOriginalName();
            $fileName = str_replace(" ", "-", $fileName);
            $category_image->move('images/category/'.$date.'/', $fileName);
            $category_image = 'images/category/'.$date.'/'.$fileName;
        }
        else{
            $category_image = $image;
        }

        $insertCategory = DB::table('categories')
                       ->where('cat_id', $category_id)
                            ->update([
                                'parent'=>$parent_id,
                                'title'=>$category_name,
                                'slug'=>$slug,
                                'level'=>$level,
                                'image'=>$category_image,
                                'status'=>$status,
                                'description'=>$desc
                               
                            ]);
        $stores = $request->stores;
    	if(!empty($stores)){
        	foreach($stores as $store_id){
            	$store_cat = DB::table('store_categories')
                    ->where('cat_id', $category_id)
                	->where('store_id',$store_id)
                    ->first();
            	
				if(!$store_cat){
                	$save_cat = DB::table('store_categories')
                            ->insertGetId([
                                'cat_id'=>$category_id,
                                'store_id'=>$store_id,
                                'created_at'=>date('Y-m-d'),
                            ]);
                }
			}
        }
        
        	$store_cats = DB::table('store_categories')
                    ->where('cat_id', $category_id)
                    ->get();
        	foreach($store_cats as $s_cat){
            	$store_exists = false;
            	if(!empty($stores)){
            		foreach($stores as $store_id){
	                	if($store_id==$s_cat->store_id){
	                    	$store_exists = true;
                        	break;
	                    }
                	}
                }
            	if(!$store_exists){
                	$delete=DB::table('store_categories')->where('cat_id',$category_id)->where('store_id',$s_cat->store_id)->delete();
                }
            }
        if($insertCategory){
            return redirect()->back()->withSuccess('Category Updated Successfully');
        }
        else{
        	if($validator->fails())
            	return redirect()->back()->withErrors("Something Wents Wrong");
	        else
            	return redirect()->back()->withSuccess('Category Updated Successfully');
        }
       
       
       
       
    }
    
    
    
 public function DeleteCategory(Request $request)
    {
        $category_id=$request->category_id;

    	$delete=DB::table('categories')->where('cat_id',$request->category_id)->delete();
        if($delete)
        {
          $deleteproduct=DB::table('product')
          ->where('cat_id',$request->category_id)->delete();
          
          $deletechild=DB::table('categories')
          ->where('parent',$request->category_id)->delete();
        
          $deleteStoreCats =DB::table('store_categories')
          ->where('cat_id',$request->category_id)->delete();
        return redirect()->back()->withSuccess('Deleted Successfully');
        }
        else
        {
           return redirect()->back()->withErrors('Unsuccessfull Delete'); 
        }
    }



 
}