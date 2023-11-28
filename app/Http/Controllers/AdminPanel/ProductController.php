<?php

namespace App\Http\Controllers\AdminPanel;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\Controller;
use App\Lib\Log\ValidationError;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\BadgeProduct;
use App\Models\BarcodeProduct;
use App\Models\Brand;
use App\Models\Category;
use App\Models\CategorySize;
use App\Models\Color;
use App\Models\Material;
use App\Models\OrderProduct;
use App\Models\PackingUnitProduct;
use App\Models\PackingUnitProductAttribute;
use App\Models\Policy;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductRate;
use App\Models\ProductStore;
use App\Models\ProductStoreStock;
use App\Models\ShippingMethod;
use App\Models\Size;
use App\Models\StockMovement;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($store_id)
    {
        $colors = Color::where('activation', 'true')->where('archive', 'false')->get();
        $store = Store::find($store_id);
        $categories = Category::whereNull('category_id')->get();
        $materials = Material::where('activation', 'true')->where('archive', 'false')->get();
        $policies = Policy::all();
        $brands = Brand::where('activation', 'true')->where('archive', 'false')->get();
        $shippings = ShippingMethod::all();
        $product_store = ProductStore::where('store_id', $store_id)->pluck('product_id');
        $products = Product::whereIn('id', $product_store)->orderBy('id', 'desc')->get();
        return view('admin.products.index', ['store' => $store, 'products' => $products, 'categories' => $categories,
            'materials' => $materials, 'policies' => $policies, 'shippings' => $shippings, 'brands' => $brands, 'colors' => $colors]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($store_id)
    {
        $colors = Color::where('activation', 'true')->where('archive', 'false')->get();
        $store = Store::find($store_id);
        $categories = Category::whereNull('category_id')->get();
        $materials = Material::where('activation', 'true')->where('archive', 'false')->get();
        $policies = Policy::all();
        $brands = Brand::where('activation', 'true')->where('archive', 'false')->get();
        $shippings = ShippingMethod::all();
        $product_store = ProductStore::where('store_id', $store_id)->pluck('product_id');
        $products = Product::whereIn('id', $product_store)->orderBy('id', 'desc')->get();
        return view('admin.products.create', ['store' => $store, 'products' => $products, 'categories' => $categories,
            'materials' => $materials, 'policies' => $policies, 'shippings' => $shippings, 'brands' => $brands, 'colors' => $colors]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $store_id)
    {
        try {
            DB::beginTransaction();
            $store = Store::find($store_id);
            $product = new Product();
            $product->name = $request->name;
            $product->description = $request->description;
            $product->material_rate = '100';
            $product->brand_id = $request->brand_id;
            $product->category_id = $request->subsubcategory_id;
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
            if ($request->discount_type == 1) {
                $product_store->net_price = $request->price - $request->discount;
            } else {
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
            if ($request->consumer_price_discount_type == 1) {
                $product_store->consumer_price = $request->consumer_old_price - $request->consumer_price_discount;
            } else {
                $discount = ($request->consumer_price_discount / 100) * $request->consumer_old_price;
                $total = $request->consumer_old_price - $discount;
                $total = number_format((float)$total, 2, '.', '');
                $product_store->consumer_price = $total;
            }
            $product_store->save();

            DB::commit();
            $request->session()->flash('status', 'تم الاضافة بنجاح');
            $url = url('admin_panel/products', $store_id);
            return redirect()->to($url)->with(array('type' => 'add_new', 'product_id' => $product['id'], 'store_id' => $store['id']));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in add product from admin panel ' . __LINE__ . $e);
            $request->session()->flash('error', 'خطأ يرجى التأكد من المشكلة');

            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($store_id, $id)
    {
        $product = Product::find($id);
        $store = Store::where('id', $store_id)->first();

        $product_store = ProductStore::where('store_id', $store['id'])->where('product_id', $product['id'])->first();
        return view('admin.products.show', ['product' => $product, 'store' => $store, 'product_store' => $product_store]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($product_id, $store_id)
    {
        $categories = Category::whereNull('category_id')->get();
        $materials = Material::where('activation', 'true')->where('archive', 'false')->get();
        $policies = Policy::all();
        $brands = Brand::where('activation', 'true')->where('archive', 'false')->get();
        $shippings = ShippingMethod::all();
        $product = \App\Models\Product::find($product_id);
        $store = \App\Models\Store::find($store_id);
        $product_store = ProductStore::where('product_id', $product_id)->where('store_id', $store_id)->first();
        $lang = app()->getLocale();
        return view('admin.products.edit', ['product' => $product, 'product_store' => $product_store, 'store' => $store, 'lang' => $lang, 'categories' => $categories,
            'materials' => $materials, 'policies' => $policies, 'shippings' => $shippings, 'brands' => $brands]);
    }

    public function product_info($product_id, $store_id)
    {
        $categories = Category::whereNotNull('category_id')->get();
        $materials = Material::where('activation', 'true')->where('archive', 'false')->get();
        $policies = Policy::all();
        $brands = Brand::where('activation', 'true')->where('archive', 'false')->get();
        $shippings = ShippingMethod::all();
        $product = \App\Models\Product::find($product_id);
        $store = \App\Models\Store::find($store_id);
        $product_store = ProductStore::where('product_id', $product_id)->where('store_id', $store_id)->first();
        $lang = app()->getLocale();
        return view('admin.products.edit', ['product' => $product, 'product_store' => $product_store, 'store' => $store, 'lang' => $lang, 'categories' => $categories,
            'materials' => $materials, 'policies' => $policies, 'shippings' => $shippings, 'brands' => $brands]);
    }

    public function product_attr($product_id, $store_id)
    {
        $categories = Category::whereNotNull('category_id')->get();
        $materials = Material::where('activation', 'true')->where('archive', 'false')->get();
        $policies = Policy::all();
        $brands = Brand::where('activation', 'true')->where('archive', 'false')->get();
        $shippings = ShippingMethod::all();
        $product = \App\Models\Product::find($product_id);
        $category = Category::find($product['category_id']);
        $store = \App\Models\Store::find($store_id);
        $product_store = ProductStore::where('product_id', $product_id)->where('store_id', $store_id)->first();
        $lang = app()->getLocale();

        $subcategory = Category::find($category['category_id']);
        $maincategory = Category::find($subcategory['category_id']);

        $not_colors = ProductStoreStock::where('product_store_id', $product_store['id'])->pluck('color_id');
        $colors = Color::where('activation', 'true')->where('archive', 'false')->whereNotIn('id', $not_colors)->get();
        return view('admin.products.add_attr', ['category' => $category, 'product' => $product, 'product_store' => $product_store, 'store' => $store, 'lang' => $lang, 'categories' => $categories,
            'materials' => $materials, 'policies' => $policies, 'shippings' => $shippings, 'brands' => $brands, 'colors' => $colors,'maincategory' => $maincategory]);
    }

    public function product_attr_save(Request $request)
    {
//        try {
        if (!isset($request['color_id'])) {
            return redirect()->back()->with('error', 'يرجى اضافة لون');
        }
        if (count($request['size_ids']) == 0) {
            return redirect()->back()->with('error', 'يرجى اضافة احجام');
        }
        $store_id = $request['store_id'];
        $product_id = $request['product_id'];
        DB::beginTransaction();
        $store = Store::find($store_id);
        $product = Product::find($product_id);
        $product_store = ProductStore::where('product_id', $product_id)->where('store_id', $store_id)->first();
        foreach ($request['size_ids'] as $size_key => $size_id) {
            if(!isset(Category::find($product->category_id)->parent->parent->id)){
                $request->session()->flash('error', 'خطأ يرجى التأكد من المشكلة');
                return redirect()->back();
            }
            $size_ids = CategorySize::where('category_id', Category::find($product->category_id)->parent->parent->id)->pluck('size_id');
            $size = Size::whereIn('id', $size_ids)->where('size', $size_id)->first();
            $product_stock = new ProductStoreStock();
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
        foreach ($request['image'] as $image_key => $image) {
            $image = new ProductImage();
            $image->product_id = $product->id;
            $image->color_id = $request->color_id;
            $image->image = UploadImage::uploadImageToStorage($request['image'][$image_key], 'products/' . $product->id);
            $image->save();
        }
        DB::commit();
        $request->session()->flash('status', 'تم الاضافة بنجاح');
        if (isset($request['type']) && $request['type'] == 'add_new') {
            return redirect()->back()->with(array('type' => 'add_new', 'product_id' => $product['id'], 'store_id' => $store['id']));
        }
        \Session::forget('type');
        return redirect()->back();

//        } catch (\Exception $e) {
//            DB::rollBack();
//            Log::error('error in add product color from admin panel ' . __LINE__ . $e);
//            $request->session()->flash('error', 'خطأ يرجى التأكد من المشكلة');
//            return redirect()->back();
//        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $product_id)
    {
        try {
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

            $product_store = ProductStore::where('product_id', $product_id)->where('store_id', $store_id)->first();
            $product_store->publish_app_at = $request->publish_app_at;
            $product_store->price = $request->price;
            $product_store->discount = $request->discount;
            $product_store->discount_type = $request->discount_type;
            if ($request->discount_type == 1) {
                $product_store->net_price = $request->price - $request->discount;
            } else {
                $discountt = ($request->discount / 100) * $request->price;
                $totall = $request->price - $discountt;
                $totall = number_format((float)$totall, 2, '.', '');
                $product_store->net_price = $totall;
            }
            $product_store->free_shipping = $request->free_shipping;
            $product_store->barcode_text = $request->barcode_text;
            if(isset($request->barcode)){
                $product_store->barcode = UploadImage::uploadImageToStorage($request->barcode, 'qr-code/stores/' . $product->id);
            }
            $product_store->consumer_price_discount = $request->consumer_price_discount;
            $product_store->consumer_price_discount_type = $request->consumer_price_discount_type;
            $product_store->consumer_old_price = $request->consumer_old_price;
            if ($request->consumer_price_discount_type == 1) {
                $product_store->consumer_price = $request->consumer_old_price - $request->consumer_price_discount;
            } else {
                $discount = ($request->consumer_price_discount / 100) * $request->consumer_old_price;
                $total = $request->consumer_old_price - $discount;
                $total = number_format((float)$total, 2, '.', '');
                $product_store->consumer_price = $total;
            }
            $product_store->save();

            DB::commit();
            $request->session()->flash('status', 'تم التعديل بنجاح');
            $url = url('admin_panel/products', $store_id);
            return redirect()->to($url);
//            $image = new ProductImage();
//            $image->product_id = $product->id;
//            $image->color_id = $request->color_id;
//            $image->image = UploadImage::uploadImageToStorage($request->image, 'products/' . $product->id);
//            $image->save();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in add product from admin panel ' . __LINE__ . $e);
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
            DB::beginTransaction();
//            PackingUnitProduct::query()->where('product_id',$id)->delete();
            BadgeProduct::query()->where('product_id',$id)->delete();
            OrderProduct::query()->where('product_id',$id)->delete();
            $unit= PackingUnitProduct::query()->where('product_id',$id)->first();
            if($unit) {
                PackingUnitProductAttribute::query()->where('packing_unit_product_id', $unit->id)->delete();
                $unit->delete();
            }
            BarcodeProduct::query()->where('product_id',$id)->delete();
            ProductRate::query()->where('product_id',$id)->delete();
            $images= ProductImage::query()->where('product_id',$id)->get();
            foreach ($images as $image){
                Storage::disk('s3')->delete($image->image);
                $image->delete();
            }
            StockMovement::query()->where('product_id',$id)->delete();
            $storeStock= ProductStore::query()->where('product_id',$id)->first();
            if($storeStock){
                ProductStoreStock::query()->where('product_store_id',$storeStock->id)->delete();
                $storeStock->delete();
            }
            Product::query()->where('id',$id)->delete();

            DB::commit();
            return response()->json([
                'type' => 'success',
                'title' => ' حذف المنتج ',
                'msg' => 'تم حذف المنتج  بنجاح',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in delete Product in dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
