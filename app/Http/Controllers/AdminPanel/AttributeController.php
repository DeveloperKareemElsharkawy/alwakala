<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\Brand;
use App\Models\Category;
use App\Models\CategorySize;
use App\Models\Color;
use App\Models\Material;
use App\Models\Policy;
use App\Models\Product;
use App\Models\ProductImage;
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
        $sizes = Size::all();
        $colors = Color::all();
        $attributes = ProductStoreStock::where('product_store_id', $product_store_id)->orderBy('id', 'desc')->get();
        $attributess = ProductStoreStock::where('product_store_id', $product_store_id)->orderBy('id', 'desc')->get()->unique('color_id');
        return view('admin.attributes.index', ['product' => $product, 'product_store' => $product_store, 'colors' => $colors,
            'sizes' => $sizes, 'store' => $store, 'attributes' => $attributes, 'attributess' => $attributess]);
    }

    public function attribute_info($attribute_id)
    {
        $attribute = ProductStoreStock::find($attribute_id);
        $product_store = ProductStore::find($attribute['product_store_id']);
        $product = Product::find($product_store['product_id']);
        $subsubCategory = Category::find($product['category_id']);
        $subcategory = Category::find($subsubCategory['category_id']);
        $category = Category::find($subcategory['category_id']);
        $store = Store::find($product_store['store_id']);
        $product_store = ProductStore::where('product_id', $product_store['product_id'])->where('store_id', $store['id'])->first();
        $lang = app()->getLocale();
        $not_colors = ProductStoreStock::where('product_store_id', $product_store['id'])->pluck('color_id');
        $colors = Color::where('activation', 'true')->where('archive', 'false')->whereNotIn('id', $not_colors)->get();
        $color = Color::find($attribute['color_id']);
        return view('admin.attributes.edit_popup', ['lang' => $lang, 'product' => $product, 'product_store' => $product_store
            , 'colors' => $colors, 'category' => $category, 'store' => $store, 'attribute' => $attribute, 'color' => $color]);
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $product_store_id)
    {
//        try{
        $found = ProductStoreStock::where('color_id', $request->color_id)->where('size_id', $request->size_id)->where('product_store_id', $product_store_id)->first();
        if ($found) {
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
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            if (!isset($request['color_id'])) {
                return redirect()->back()->with('error', 'يرجى اضافة لون');
            }
            $store_id = $request['store_id'];
            $product_id = $request['product_id'];
            DB::beginTransaction();
            $store = Store::find($store_id);
            $product = Product::find($product_id);
            $product_store = ProductStore::where('product_id', $product_id)->where('store_id', $store_id)->first();
            $size_ids = CategorySize::where('category_id', $product['category_id'])->pluck('size_id');
            foreach ($request['size_ids'] as $size_key => $size_id) {
                $size = Size::whereIn('id', $size_ids)->where('size', $size_id)->first();
                $product_stock = ProductStoreStock::where('size_id', $size['id'])->where('color_id', $request['color_id'])->where('product_store_id', $product_store['id'])->orderBy('id', 'desc')->first();
                $product_stock->product_store_id = $product_store['id'];
                $product_stock->stock = $request['size_counts'][$size_key];
                $product_stock->size_id = $size['id'];
                $product_stock->color_id = $request['color_id'];
                $product_stock->reserved_stock = 0;
                $product_stock->available_stock = $request['size_counts'][$size_key];
                $product_stock->sold = 0;
                $product_stock->returned = 0;
                $product_stock->approved = true;
                $product_stock->save();
            }
            if (isset($request['image'])) {
                foreach ($request['image'] as $image_key => $image) {
                    if (isset($request['image'][$image_key])) {
                        $image = new ProductImage();
                        $image->product_id = $product->id;
                        $image->color_id = $request->color_id;
                        $image->image = UploadImage::uploadImageToStorage($request['image'][$image_key], 'products/' . $product->id);
                        $image->save();
                    }
                }
            }

            DB::commit();
            $request->session()->flash('status', 'تم التعديل بنجاح');
            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in edit product color from admin panel ' . __LINE__ . $e);
            $request->session()->flash('error', 'خطأ يرجى التأكد من المشكلة');
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $attr = ProductStoreStock::find($id);
            if ($attr) {
                $store_stocks = DB::table('product_store_stock')->where('color_id' , $attr['color_id'])->where('product_store_id' , $attr['product_store_id'])->delete();
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
