<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function list(Request $request)
    {
        $title = "Product List";
         $admin_email=Session::get('bamaAdmin');
    	 $admin= DB::table('admin')
    	 		   ->where('admin_email',$admin_email)
    	 		   ->first();
    	  $logo = DB::table('tbl_web_setting')
                ->where('set_id', '1')
                ->first();
           $product = DB::table('product')
                    ->join('categories','product.cat_id','=','categories.cat_id')
                   ->get();
        
    	return view('admin.product.list', compact('title',"admin", "logo","product"));
    }

    
     public function AddProduct(Request $request)
    {
    
        $title = "Add Product";
         $admin_email=Session::get('bamaAdmin');
    	 $admin= DB::table('admin')
    	 		   ->where('admin_email',$admin_email)
    	 		   ->first();
    	  $logo = DB::table('tbl_web_setting')
                ->where('set_id', '1')
                ->first();
           $cat = DB::table('categories')
                   ->select('parent')
                   ->get();
                   
        if(count($cat)>0){           
        foreach($cat as $cats) {
            $a = $cats->parent;
           $aa[] = array($a); 
        }
        }
        else{
            $a = 0;
           $aa[] = array($a);
        }
    
    	$category = array();
    
    	$cats = DB::table('categories')
                  ->where('parent', '=', 0)
                  ->get();
    	foreach($cats as $cat){
        	array_push($category,$cat);
        	$subcats = DB::table('categories')
                   ->where('parent', '=', $cat->cat_id)
                   ->get();	
        	if(!empty($subcats)){
            	foreach($subcats as $subcat){
                	$subcat = json_decode(json_encode($subcat), true);
                	$subcat['title'] = " - ".$subcat['title'];
                	array_push($category,$subcat);
                	$subcats2 = DB::table('categories')
                   		->where('parent', '=', $subcat['cat_id'])
                   		->get();
                	if(!empty($subcats2)){
                		foreach($subcats2 as $subcat){
                        	$subcat = json_decode(json_encode($subcat), true);
                    		$subcat['title'] = " -- ".$subcat['title'];
                    		array_push($category,$subcat);
                		}
            		}
                }
            }
        }
     	$category = json_decode(json_encode($category), false);
     	$city = DB::table('city')
                ->get();
     	$stores= DB::table('store')->get();
        
         /*$category = DB::table('categories')
                  // ->where('level', '!=', 0)
                  // ->WhereNotIn('cat_id',$aa)
                    ->get();*/
   
        
        return view('admin.product.add',compact("category", "admin_email","logo", "admin","title","city",'stores'));
     }
    
     public function AddNewProduct(Request $request)
    {
        $category_id=$request->cat_id;
        $product_name = $request->product_name;
        $quantity = $request->quantity;
        $unit = $request->unit;
        $price = $request->price;
        $description = $request->description;
        $date=date('d-m-Y');
        $mrp = $request->mrp;
        $weight = $request->weight;
        
        if($weight == NULL){
            $weight = 0;
        }
    
    
        
        $this->validate(
            $request,
                [
                    'cat_id'=>'required',
                    'product_name' => 'required',
                    'product_image' => 'required|mimes:jpeg,png,jpg|max:1000',
                    'quantity'=> 'required',
                    'unit'=> 'required',
                    //'price'=> 'required',
                    'mrp'=>'required',
                ],
                [
                    'cat_id.required'=>'Select category',
                    'product_name.required' => 'Enter product name.',
                    'product_image.required' => 'Choose product image.',
                    'quantity.required' => 'Enter quantity.',
                    'unit.required' => 'Choose unit.',
                    //'price.required' => 'Enter price.',
                    'mrp.required'=>'Enter MRP.',
                ]
        );


        if($request->hasFile('product_image')){
            $product_image = $request->product_image;
            $fileName = $product_image->getClientOriginalName();
            $fileName = str_replace(" ", "-", $fileName);
            $product_image->move('images/product/'.$date.'/', $fileName);
            $product_image = 'images/product/'.$date.'/'.$fileName;
        }
        else{
            $category_image = 'N/A';
        }

        $insertproduct = DB::table('product')
                            ->insertGetId([
                                'cat_id'=>$category_id,
                                'product_name'=>$product_name,
                                'product_image'=>$product_image,
                                
                               
                            ]);
        $price = $mrp;
        if($insertproduct){
             $varient_id = DB::table('product_varient')
            ->insertGetId([
                'product_id'=>$insertproduct,
                'quantity'=>$quantity,
                'varient_image'=>$product_image,
                'unit'=>$unit,
                'base_price'=>$price,
                'base_mrp'=>$mrp,
                'weight'=>$weight,
                'description'=>$description,
            ]);
        
        	$stores = $request->stores;
        	if(!empty($stores)){
	            foreach($stores as $store_id){
	                $save_cat = DB::table('store_products')
	                            ->insertGetId([
	                                'varient_id'=>$varient_id,
	                                'store_id'=>$store_id,
	                                'price'=>$price,
                                	'mrp'=>$mrp,
                                	'stock'=>0
	                            ]);
	            }
	        }
            
            return redirect()->back()->withSuccess('Product Added Successfully');
        }
        else{
            return redirect()->back()->withErrors("Something Wents Wrong");
        }
      
    }
    
    public function EditProduct(Request $request)
    {
         $product_id = $request->product_id;
         $title = "Edit Product";
         $admin_email=Session::get('bamaAdmin');
    	 $admin= DB::table('admin')
    	 		   ->where('admin_email',$admin_email)
    	 		   ->first();
    	  $logo = DB::table('tbl_web_setting')
                ->where('set_id', '1')
                ->first();
          $product = DB::table('product')
          		   ->join('product_varient','product_varient.product_id','product.product_id')
                   ->where('product.product_id',$product_id)
                    ->first();
    		$city = DB::table('city')
                ->get();
    
    	$varient_id = DB::table('product_varient')->select('varient_id')->where('product_id',$product_id)->first()->varient_id;
    	$stores = DB::table('store')
					->select('store.store_id','store.store_name','store.employee_name','store.city',DB::Raw('IFNULL(`store_products`.`varient_id`,0) as selected'))
					->leftJoin('store_products',function($join)use ($varient_id){
                    $join->on('store_products.store_id','=','store.store_id')->where('store_products.varient_id',$varient_id);})
					->get();
                    

        return view('admin.product.edit',compact("admin_email","admin","logo","title","product","city","stores"));
    }

    public function UpdateProduct(Request $request)
    {
        $product_id = $request->product_id;
        $product_name = $request->product_name;
        $date=date('d-m-Y');
        $product_image = $request->product_image;
    	$description = $request->description;
    	$weight = $request->weight;
    	$quantity = $request->quantity;
    	$unit = $request->unit;
    	
    	$validator = Validator::make($request->all(),
                [
                    'product_name' => 'required',
                ],
                [
                    'product_name.required' => 'Enter product name.',
                ]);
        
        $this->validate(
            $request,
                [ 
                    'product_name' => 'required',
                ],
                [
                    'product_name.required' => 'Enter product name.',
                ]
        );

       $getProduct = DB::table('product')
                    ->where('product_id',$product_id)
                    ->first();

        $image = $getProduct->product_image;

        if($request->hasFile('product_image')){
            $product_image = $request->product_image;
            $fileName = $product_image->getClientOriginalName();
            $fileName = str_replace(" ", "-", $fileName);
            $product_image->move('images/product/'.$date.'/', $fileName);
            $product_image = 'images/product/'.$date.'/'.$fileName;
        }
        else{
            $product_image = $image;
        }

        $insertproduct = DB::table('product')
                       ->where('product_id', $product_id)
                            ->update([
                                'product_name'=>$product_name,
                                'product_image'=>$product_image,
                            	'city_id'=>$request->city
                            ]);
    	$updateDesc = DB::table('product_varient')
        				->where('product_id',$product_id)
        				->update([
        				            'quantity'=>$quantity,
        				            'unit'=>$unit,
        				            'description'=>$description,
        				            'weight'=>$weight,
                                 'varient_image'=>$product_image
                                 ]);
        
    	$varient = DB::table('product_varient')->select('varient_id','base_price','base_mrp')->where('product_id',$product_id)->first();
    
    	$stores = $request->stores;
    	if(!empty($stores)){
        	foreach($stores as $store_id){
            	$store_product = DB::table('store_products')
                    ->where('varient_id', $varient->varient_id)
                	->where('store_id',$store_id)
                    ->first();
            	
				if(!$store_product){
                	$save_product = DB::table('store_products')
                            ->insertGetId([
                                'varient_id'=>$varient->varient_id,
                                'store_id'=>$store_id,
                                'price'=>$varient->base_price,
                            	'mrp'=>$varient->base_mrp
                            ]);
                }
			}
        }
        
        	$store_product = DB::table('store_products')
                    ->where('varient_id', $varient->varient_id)
                    ->get();
        	foreach($store_product as $s_product){
            	$store_exists = false;
            	if(!empty($stores)){
            		foreach($stores as $store_id){
	                	if($store_id==$s_product->store_id){
	                    	$store_exists = true;
                        	break;
	                    }
                	}
                }
            	if(!$store_exists){
                	$delete=DB::table('store_products')->where('varient_id',$varient->varient_id)->where('store_id',$s_product->store_id)->delete();
                }
            }
    
        if($insertproduct){
            return redirect()->back()->withSuccess('Product Updated Successfully');
        }
        else{
        	if($validator->fails())
            	return redirect()->back()->withErrors("Something Wents Wrong");
        	else
            	return redirect()->back()->withSuccess('Product Updated Successfully');
        }
       
       
       
       
    }
    
    
    
 public function DeleteProduct(Request $request)
    {
        $product_id=$request->product_id;

    	$delete=DB::table('product')->where('product_id',$request->product_id)->delete();
        if($delete)
        {
         $delete=DB::table('product_varient')->where('product_id',$request->product_id)->delete();  
         
        return redirect()->back()->withSuccess('Deleted Successfully');
        }
        else
        {
           return redirect()->back()->withErrors('Unsuccessfull Delete'); 
        }
    }

}