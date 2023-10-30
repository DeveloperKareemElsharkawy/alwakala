<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Material;
use App\Models\Policy;
use App\Models\Product;
use App\Models\ProductStore;
use App\Models\ProductStoreStock;
use App\Models\ShippingMethod;
use App\Models\Size;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($product_store_id)
    {
        $product_store = ProductStore::find($product_store_id);
        $product = Product::find($product_store['product_id']);
        $store = Store::find($product_store['store_id']);
        $sizes  = Size::all();
        $colors = Color::all();
        $attributes = ProductStoreStock::where('product_store_id' , $product_store_id)->orderBy('id' , 'desc')->get();
        return view('admin.attributes.index' , ['product'=>$product , 'product_store'=>$product_store,'colors'=>$colors,
            'sizes'=>$sizes,'store'=>$store,'attributes'=>$attributes]);
    }

    public function attribute_info($attribute_id)
    {
        $attribute = ProductStoreStock::find($attribute_id);
        $product_store = ProductStore::find($attribute['product_store_id']);
        $product = Product::find($product_store['product_id']);
        $store = Store::find($product_store['store_id']);
        $sizes  = Size::all();
        $colors = Color::all();
        $lang = app()->getLocale();
        return view('admin.attributes.edit_popup' , ['lang'=>$lang,'product'=>$product , 'product_store'=>$product_store,'colors'=>$colors,
            'sizes'=>$sizes,'store'=>$store,'attribute'=>$attribute]);
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
    public function store(Request $request,$product_store_id)
    {
//        try{
            $found = ProductStoreStock::where('color_id',$request->color_id)->where('size_id',$request->size_id)->where('product_store_id',$product_store_id)->first();
            if($found){
                $request->session()->flash('error', 'تم الاضافة مسبقا');
                return redirect()->back();
            }
            DB::beginTransaction();
            $product = new ProductStoreStock();
            $product->stock = $request->stock;
            $product->color_id = $request->color_id;
            $product->size_id = $request->size_id;
            $product->product_store_id = $product_store_id;
            $product->reserved_stock = 0;
            $product->available_stock = $request->stock;
            $product->sold = 0;
            $product->returned = 0;
            $product->save();
            DB::commit();
            $request->session()->flash('status', 'تم الاضافة بنجاح');
            return redirect()->back();
//            $image = new ProductImage();
//            $image->product_id = $product->id;
//            $image->color_id = $request->color_id;
//            $image->image = UploadImage::uploadImageToStorage($request->image, 'products/' . $product->id);
//            $image->save();
//        }catch (\Exception $e){
//            DB::rollBack();
//            Log::error('error in add product from admin panel ' . __LINE__ . $e);
//            $request->session()->flash('error', 'خطأ يرجى التأكد من المشكلة');
//
//            return redirect()->back();
//        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            $product_store_id  = $request['product_store_id'];
            $found = ProductStoreStock::where('id' , '!=' , $id)->where('color_id',$request->color_id)->where('size_id',$request->size_id)->where('product_store_id',$product_store_id)->first();
            if($found){
                $request->session()->flash('error', 'تم الاضافة مسبقا');
                return redirect()->back();
            }
            DB::beginTransaction();
            $product = ProductStoreStock::find($id);
            $product->stock = $request->stock;
            $product->color_id = $request->color_id;
            $product->size_id = $request->size_id;
            $product->reserved_stock = 0;
            $product->available_stock = $request->stock;
            $product->sold = 0;
            $product->returned = 0;
            $product->save();
            DB::commit();
            $request->session()->flash('status', 'تم التعديل بنجاح');
            return redirect()->back();
//            $image = new ProductImage();
//            $image->product_id = $product->id;
//            $image->color_id = $request->color_id;
//            $image->image = UploadImage::uploadImageToStorage($request->image, 'products/' . $product->id);
//            $image->save();
        }catch (\Exception $e){
            DB::rollBack();
            Log::error('error in edit attribute from admin panel ' . __LINE__ . $e);
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
        try {
            $attr = ProductStoreStock::find($id);
            if ($attr) {
                $attr->delete();
                return response()->json([
                    'type' => 'success',
                    'title' => ' حذف الخاصية نهائيا',
                    'msg' => 'تم حذف الخاصية بنجاح',
                ]);
            } else {
                return response()->json([
                    'type' => 'error',
                    'title' => ' حذف الخاصية',
                    'msg' => 'الخاصية غير موجودة'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'title' => 'مشكلة من الخادم',
                'msg' => 'يوجد مشكلة يرجى الرجوع الى المطور'
            ]);
        }
    }
}
