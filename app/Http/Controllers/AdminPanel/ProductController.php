<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Material;
use App\Models\Policy;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductStore;
use App\Models\ShippingMethod;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($store_id)
    {
        $store = Store::find($store_id);
        $categories = Category::whereNotNull('category_id')->get();
        $materials = Material::all();
        $policies = Policy::all();
        $brands = Brand::all();
        $shippings = ShippingMethod::all();
        $product_store = ProductStore::where('store_id' , $store_id)->pluck('product_id');
        $products = Product::whereIn('id' , $product_store)->orderBy('id','desc')->get();
        return view('admin.products.index' , ['store' => $store,'products'=>$products,'categories'=>$categories,
            'materials' =>$materials , 'policies'=>$policies ,'shippings'=>$shippings,'brands'=>$brands]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $store_id)
    {
        try{
            DB::beginTransaction();
            $store = Store::find($store_id);
            $product = new Product();
            $product->name = $request->name;
            $product->description = $request->description;
            $product->material_rate = '100';
            $product->brand_id = $request->brand_id;
            $product->category_id = $request->category_id;
            $product->owner_id = $store->user_id;
            $product->reviewed = true;
            $product->channel = 'seller-app';
            $product->consumer_price = $request->consumer_price;
            $product->material_id = $request->material_id;
            $product->policy_id = $request->policy_id;
            $product->shipping_method_id = $request->shipping_method_id;
            $product->youtube_link = $request->youtube_link;
            $product->save();

            $product_store = new ProductStore();
            $product_store->product_id = $product->id;
            $product_store->store_id = $store_id;
            $product_store->publish_app_at = $request->publish_app_at;
            $product_store->views = 0;
            $product_store->price = $request->price;
            $product_store->discount = $request->discount;
            $product_store->discount_type = $request->discount_type;
            if($request->discount_type == 1){
                $product_store->net_price = $request->price - $request->discount;
            }else{
                $discountt = ($request->discount / 100) * $request->price;
                $totall = $request->price - $discountt;
                $totall = number_format((float)$totall, 2, '.', '');
                $product_store->net_price = $totall;
            }
            $product_store->free_shipping = $request->free_shipping;
            $product_store->barcode_text = $request->barcode_text;
            $product_store->barcode = UploadImage::uploadImageToStorage($request->barcode, 'qr-code/stores/' . $product->id);
            $product_store->consumer_price_discount = $request->consumer_price_discount;
            $product_store->consumer_price_discount_type = $request->consumer_price_discount_type;
            $product_store->consumer_old_price = $request->consumer_old_price;
            if($request->consumer_price_discount_type == 1){
                $product_store->consumer_price = $request->consumer_old_price - $request->consumer_price_discount;
            }else{
                $discount = ($request->consumer_price_discount / 100) * $request->consumer_old_price;
                $total = $request->consumer_old_price - $discount;
                $total = number_format((float)$total, 2, '.', '');
                $product_store->consumer_price = $total;
            }
            $product_store->save();

            DB::commit();
            $request->session()->flash('status', 'تم الاضافة بنجاح');
            return redirect()->back();
//            $image = new ProductImage();
//            $image->product_id = $product->id;
//            $image->color_id = $request->color_id;
//            $image->image = UploadImage::uploadImageToStorage($request->image, 'products/' . $product->id);
//            $image->save();
        }catch (\Exception $e){
            DB::rollBack();
            Log::error('error in add product from admin panel ' . __LINE__ . $e);
            $request->session()->flash('error', 'خطأ يرجى التأكد من المشكلة');

            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($store_id , $id)
    {
        $product = Product::find($id);
        $store = Store::where('id',$store_id)->first();

        $product_store = ProductStore::where('store_id' , $store['id'])->where('product_id' , $product['id'])->first();
        return view('admin.products.show' , ['product'=>$product,'store'=>$store,'product_store'=>$product_store]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($product_id , $store_id)
    {
        $categories = Category::whereNotNull('category_id')->get();
        $materials = Material::all();
        $policies = Policy::all();
        $brands = Brand::all();
        $shippings = ShippingMethod::all();
        $product = \App\Models\Product::find($product_id);
        $store = \App\Models\Store::find($store_id);
        $product_store = ProductStore::where('product_id' , $product_id)->where('store_id' , $store_id)->first();
        $lang = app()->getLocale();
        return view('admin.products.edit', ['product'=>$product,'product_store'=>$product_store,'store' => $store,'lang'=>$lang,'categories'=>$categories,
            'materials' =>$materials , 'policies'=>$policies ,'shippings'=>$shippings,'brands'=>$brands]);
    }

    public function product_info($product_id , $store_id)
    {
        $categories = Category::whereNotNull('category_id')->get();
        $materials = Material::all();
        $policies = Policy::all();
        $brands = Brand::all();
        $shippings = ShippingMethod::all();
        $product = \App\Models\Product::find($product_id);
        $store = \App\Models\Store::find($store_id);
        $product_store = ProductStore::where('product_id' , $product_id)->where('store_id' , $store_id)->first();
        $lang = app()->getLocale();
        return view('admin.products.edit', ['product'=>$product,'product_store'=>$product_store,'store' => $store,'lang'=>$lang,'categories'=>$categories,
            'materials' =>$materials , 'policies'=>$policies ,'shippings'=>$shippings,'brands'=>$brands]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $product_id)
    {
        try{
            $store_id = $request['store_id'];
            DB::beginTransaction();
            $store = Store::find($store_id);
            $product = Product::find($product_id);
            $product->name = $request->name;
            $product->description = $request->description;
            $product->material_rate = '100';
            $product->brand_id = $request->brand_id;
            $product->category_id = $request->category_id;
            $product->owner_id = $store->user_id;
            $product->reviewed = true;
            $product->channel = 'seller-app';
            $product->consumer_price = $request->consumer_price;
            $product->material_id = $request->material_id;
            $product->policy_id = $request->policy_id;
            $product->shipping_method_id = $request->shipping_method_id;
            $product->youtube_link = $request->youtube_link;
            $product->save();

            $product_store = ProductStore::where('product_id', $product_id)->where('store_id' , $store_id)->first();
            $product_store->publish_app_at = $request->publish_app_at;
            $product_store->price = $request->price;
            $product_store->discount = $request->discount;
            $product_store->discount_type = $request->discount_type;
            if($request->discount_type == 1){
                $product_store->net_price = $request->price - $request->discount;
            }else{
                $discountt = ($request->discount / 100) * $request->price;
                $totall = $request->price - $discountt;
                $totall = number_format((float)$totall, 2, '.', '');
                $product_store->net_price = $totall;
            }
            $product_store->free_shipping = $request->free_shipping;
            $product_store->barcode_text = $request->barcode_text;
            $product_store->barcode = UploadImage::uploadImageToStorage($request->barcode, 'qr-code/stores/' . $product->id);
            $product_store->consumer_price_discount = $request->consumer_price_discount;
            $product_store->consumer_price_discount_type = $request->consumer_price_discount_type;
            $product_store->consumer_old_price = $request->consumer_old_price;
            if($request->consumer_price_discount_type == 1){
                $product_store->consumer_price = $request->consumer_old_price - $request->consumer_price_discount;
            }else{
                $discount = ($request->consumer_price_discount / 100) * $request->consumer_old_price;
                $total = $request->consumer_old_price - $discount;
                $total = number_format((float)$total, 2, '.', '');
                $product_store->consumer_price = $total;
            }
            $product_store->save();

            DB::commit();
            $request->session()->flash('status', 'تم التعديل بنجاح');
            $url = url('admin_panel/products',$store_id);
            return redirect()->to($url);
//            $image = new ProductImage();
//            $image->product_id = $product->id;
//            $image->color_id = $request->color_id;
//            $image->image = UploadImage::uploadImageToStorage($request->image, 'products/' . $product->id);
//            $image->save();
        }catch (\Exception $e){
            DB::rollBack();
            Log::error('error in add product from admin panel ' . __LINE__ . $e);
            $request->session()->flash('error', 'خطأ يرجى التأكد من المشكلة');

            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
