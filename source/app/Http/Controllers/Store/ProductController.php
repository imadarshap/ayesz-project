<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Session;

class ProductController extends Controller
{
    public function sel_product(Request $request)
    {
        $title = "Add Product";
         $email=Session::get('bamaStore');
    	 $store= DB::table('store')
    	 		   ->where('email',$email)
    	 		   ->first();
    if(empty($store)){
        	return redirect('store/logout');
        }
    	  $logo = DB::table('tbl_web_setting')
                ->where('set_id', '1')
                ->first();
         
        $selected =  DB::table('store_products')
                ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                ->join('product', 'product_varient.product_id', '=', 'product.product_id')
        ->join('categories', 'product.cat_id', '=', 'categories.cat_id')
                ->where('store_products.store_id', $store->store_id)
                ->orderByRaw('store_products.stock=0,product.product_name')
                ->paginate(8);  
                
        $check=  DB::table('store_products')
                ->where('store_id', $store->store_id)
                ->get(); 
        if(count($check)>0)  {
        foreach($check as $ch){
            $ch2 = $ch->varient_id;
            $ch3[] = array($ch2);
        }
            $products = DB::table('product_varient')
                ->join('product','product_varient.product_id', '=', 'product.product_id')
                ->whereNotIn('product_varient.varient_id', $ch3)
                ->get();    
        
    	    return view('store.products.select', compact('title',"store", "logo","products","selected"));
        }else{
            $products = DB::table('product_varient')
                ->join('product','product_varient.product_id', '=', 'product.product_id')
                ->get();
                
            return view('store.products.select', compact('title',"store", "logo","products","selected"));
        }
      
    }

	public function add_multiple_products(Request $request){
    	$title = "Add Multiple Products";
         $email=Session::get('bamaStore');
    	 $store= DB::table('store')
    	 		   ->where('email',$email)
    	 		   ->first();
    	  $logo = DB::table('tbl_web_setting')
                ->where('set_id', '1')
                ->first();
         
        $selected =  DB::table('store_products')
                ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                ->where('store_products.store_id', $store->store_id)
                ->orderBy('store_products.stock','asc')
                ->paginate(8);  
                
        $check=  DB::table('store_products')
                ->where('store_id', $store->store_id)
                ->get(); 
        if(count($check)>0)  {
        foreach($check as $ch){
            $ch2 = $ch->varient_id;
            $ch3[] = array($ch2);
        }
          $products = DB::table('product_varient')
                ->join('product','product_varient.product_id', '=', 'product.product_id')
                ->whereNotIn('product_varient.varient_id', $ch3)
                ->get();    
        
    	return view('store.products.add_multiple_products', compact('title',"store", "logo","products","selected"));
        }else{
             $products = DB::table('product_varient')
                ->join('product','product_varient.product_id', '=', 'product.product_id')
                ->get();
                
            return view('store.products.add_multiple_products', compact('title',"store", "logo","products","selected"));    
        }
    }

	public function added_multiple_products(Request $request){
    	$email=Session::get('bamaStore');
    	 $store= DB::table('store')
    	 		   ->where('email',$email)
    	 		   ->first();
    	 		   
    	 $logo = DB::table('tbl_web_setting')
                ->where('set_id', '1')
                ->first();
          
    $prod = $request->prod;
    
    $countprod = count($prod);

    for($i=0;$i<=($countprod-1);$i++)
        {
            $varient_id = $prod[$i];
            $pr= DB::table('product_varient')
                 ->where('varient_id',$varient_id)
                 ->first();
                 
            $insert2 = DB::table('store_products')
                  ->insert(['store_id'=>$store->store_id,'stock'=>0, 'varient_id'=>$prod[$i], 'price'=>$pr->base_price,'mrp'=>$pr->base_mrp]);
        }     
          
         return redirect()->back()->withSuccess('Products Added Successfully');
    }
    
    
    
      public function st_product(Request $request)
    {
        $title = "Products";
         $email=Session::get('bamaStore');
    	 $store= DB::table('store')
    	 		   ->where('email',$email)
    	 		   ->first();
      	if(empty($store)){
        	return redirect('store/logout');
        }
    	  $logo = DB::table('tbl_web_setting')
                ->where('set_id', '1')
                ->first();
         
        $selected =  DB::table('store_products')
                ->join('product_varient', 'store_products.varient_id', '=', 'product_varient.varient_id')
                ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                ->where('store_id', $store->store_id)
                ->orderBy('store_products.stock','asc')
                ->get();  
                
        $check=  DB::table('store_products')
                ->where('store_id', $store->store_id)
                ->get(); 
        if(count($check)>0)  {
        foreach($check as $ch){
            $ch2 = $ch->varient_id;
            $ch3[] = array($ch2);
        }
          $products = DB::table('product_varient')
                ->join('product','product_varient.product_id', '=', 'product.product_id')
                ->whereNotIn('product_varient.varient_id', $ch3)
                ->get();    
        
    	return view('store.products.pr', compact('title',"store", "logo","products","selected"));
        }else{
             $products = DB::table('product_varient')
                ->join('product','product_varient.product_id', '=', 'product.product_id')
                ->get();
                
            return view('store.products.pr', compact('title',"store", "logo","products","selected"));    
        }
      
    }
    
    
    public function added_product(Request $request)
    {
         $email=Session::get('bamaStore');
    	 $store= DB::table('store')
    	 		   ->where('email',$email)
    	 		   ->first();
    if(empty($store)){
        	return redirect('store/logout');
        }
    	 		   
    	 $logo = DB::table('tbl_web_setting')
                ->where('set_id', '1')
                ->first();
          
    $product_id = $request->product_id;
    $mrp = $request->mrp;
    $price = $request->price;
    $stock = $request->stock;
    $insert2 = DB::table('store_products')
                  ->insert(['store_id'=>$store->store_id,'stock'=>0, 'varient_id'=>$product_id, 'price'=>$price,'mrp'=>$mrp,'stock'=>$stock]);
    if($insert2)
    	return redirect()->back()->withSuccess('Product Added Successfully');
    else
    	return redirect()->back()->withErrors('Something Went Wrong');
    }
    
     public function delete_product(Request $request)
    {
        $id =$request->id;
    	 $delete = DB::table('store_products')
                ->where('p_id', $id)
                ->delete();
         if($delete){
            return redirect()->back()->withSuccess('Product Removed'); 
         } else{
         return redirect()->back()->withErrors('Something Went Wrong');
         }

    }
    
     public function stock_update(Request $request)
    {
        $id =$request->id;
        $stock = $request->stock;
    	 $stockupdate = DB::table('store_products')
                ->where('p_id', $id)
                ->update(['stock'=>$stock]);
         if($stockupdate){
            return redirect()->back()->withSuccess('Product Stock Updated Successfully'); 
         } else{
         return redirect()->back()->withErrors('something went wrong');
         }
    }
}
