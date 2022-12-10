<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\StoreTypes\StoreType;
use App\Http\Controllers\BaseController;
use App\Lib\Helpers\Authorization\AuthorizationHelper;
use App\Lib\Log\ServerError;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\BarcodeProduct;
use App\Models\Bundle;
use App\Models\PackingUnitProduct;
use App\Models\PackingUnitProductAttribute;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductStore;
use App\Models\ProductStoreStock;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Lib\Log\ValidationError;
use App\Http\Requests\Inventory\UpdateInfoRequest;
use App\Http\Controllers\Controller;

class UpdateInventoryController extends BaseController
{
    public function updateInfo(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:products,id',
                'name' => 'required|string|max:255',
                'brand_id' => 'nullable|exists:brands,id',
                'category_id' => 'required|exists:categories,id',
                'consumer_price' => 'nullable|numeric',
                'shipping_method_id' => 'required|exists:shipping_methods,id',
                'policy_id' => 'required|exists:policies,id',
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            if (!AuthorizationHelper::isAuthorized('id', $request->id, 'owner_id', $request->user_id, Product::class)) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.inventory.owner_update_info_limit'),
                    'data' => ''
                ], AResponseStatusCode::FORBIDDEN);
            }

            $product = Product::query()->find($request->id);
            $product->name = $request->name;
            $product->brand_id = $request->brand_id;
            $product->category_id = $request->category_id;
            $product->consumer_price = $request->consumer_price;
            $product->description = $request->description;
            $product->policy_id = $request->policy_id;
            $product->shipping_method_id = $request->shipping_method_id;
            $product->save();

            return response()->json([
                'status' => true,
                'message' =>  trans('messages.product.updated'),
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in updateInfo of seller UpdateInventory' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function updateStoreDetails(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:product_store,id',
                'publish_app_at' => 'required|date_format:Y-m-d',
                'price' => 'nullable|numeric',
                'net_price' => 'nullable|numeric',
                'discount' => 'nullable|numeric',
                'discount_type' => 'nullable|in:1,2',
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            DB::beginTransaction();
            $product = ProductStore::query()->find($request->id);
            $product->publish_app_at = $request->publish_app_at;
            $product->price = $request->price;
            $product->net_price = $request->net_price;
            $product->discount = $request->discount;
            $product->discount_type = $request->discount_type;
            $product->save();

            $store = Store::query()
                ->select('id', 'store_type_id')
                ->where('user_id', $request->user_id)->first();
            if ($store->store_type_id == StoreType::SUPPLIER) {
                Bundle::query()
                    ->where('product_id', $product->product_id)
                    ->where('store_id', $store->id)
                    ->update(['price' => $request->price]);
            }
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => trans('messages.stores.updated'),
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in updateStoreDetails of seller UpdateInventory' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function updateBarcode(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:barcode_product,id',
                'barcode' => 'required|string|max:255',
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            $product = BarcodeProduct::query()->find($request->id);

            if (!AuthorizationHelper::isAuthorized('id', $product->product_id, 'owner_id', $request->user_id, Product::class)) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.barcode.update_limit'),
                    'data' => ''
                ], AResponseStatusCode::FORBIDDEN);
            }
            $product->barcode = $request->barcode;
            $product->save();

            return response()->json([
                'status' => true,
                'message' =>  trans('messages.barcode.updated'),
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in updateBarcode of seller UpdateInventory' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function updateBundlePrice(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:bundles,id',
                'quantity' => 'required|numeric',
                'price' => 'nullable|numeric',
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            $storeId = Store::query()->select('id')->where('user_id', $request->user_id)->first()->id;
            if (!AuthorizationHelper::isAuthorized('id', $request->id, 'store_id', $storeId, Bundle::class)) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.barcode.update_limit'),
                    'data' => ''
                ], AResponseStatusCode::FORBIDDEN);
            }
            DB::beginTransaction();

            $product = Bundle::query()->find($request->id);


            $prices = Bundle::query()
                ->where('product_id', $product->product_id)
                ->where('store_id', $product->store_id)
                ->get();
            $lowestPrice = 99999999;
            foreach ($prices as $price) {
                if ($price->price < $lowestPrice) {
                    $lowestPrice = $price->price;
                }
            }

            if ($lowestPrice == $product->price) {
                ProductStore::query()
                    ->where('product_id', $product->product_id)
                    ->where('store_id', $product->store_id)
                    ->update(['price' => $request->price]);
            }

            $product->price = $request->price;
            $product->quantity = $request->quantity;
            $product->save();
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => trans('messages.product.price_updated'),
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in updateBundlePrice of seller UpdateInventory' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function updateStock(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:product_store_stock,id',
                'available_stock' => 'required|numeric|min:0',
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            $product = ProductStoreStock::query()->find($request->id);
            $storeId = Store::query()->where('user_id', $request->user_id)->first()->id;
            if (!ProductStore::query()
                ->where('id', $product->product_store_id)
                ->where('store_id', $storeId)->first()) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.product.product_stock_owner_update_limit'),
                    'data' => ''
                ], AResponseStatusCode::FORBIDDEN);
            }
//            $value = 0;
//            if ($request->available_stock > $product->available_stock) {
//                $value = $request->available_stock - $product->available_stock;
//            } elseif ($request->available_stock < $product->available_stock) {
//                $value = $product->available_stock - $request->available_stock;
//                $value *= -1;
//            }
            $product->available_stock = $request->available_stock;
//            $product->stock += $value;
            $product->save();

            return response()->json([
                'status' => true,
                'message' => trans('messages.inventory.stock_updated'),
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in updateStock of seller UpdateInventory' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function updatePackage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:packing_unit_product_attributes,id',
                'size_id' => 'required|numeric|exists:sizes,id',
                'quantity' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            // because who edit package should be owner
            $productIds = Product::query()
                ->where('owner_id', $request->user_id)
                ->pluck('id')->toArray();

            $product = PackingUnitProductAttribute::query()->find($request->id);
            $productId = PackingUnitProduct::query()
                ->where('id', $product->packing_unit_product_id)
                ->first()->product_id;

            if (!in_array($productId, $productIds)) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.package.update_limit'),
                    'data' => ''
                ], AResponseStatusCode::FORBIDDEN);
            }
            $product->size_id = $request->size_id;
            $product->quantity = $request->quantity;
            $product->save();

            return response()->json([
                'status' => true,
                'message' => trans('messages.package.updated'),
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in updatePackage of seller UpdateInventory' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function addImage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|numeric|exists:products,id',
                'color_id' => 'required|numeric|exists:colors,id',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            if (!AuthorizationHelper::isAuthorized('id', $request->product_id, 'owner_id', $request->user_id, Product::class)) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.inventory.update_image_limit'),
                    'data' => ''
                ], AResponseStatusCode::FORBIDDEN);
            }
            //TODO check on images if there is 3
            $image = new ProductImage;
            $image->image = UploadImage::uploadImageToStorage($request->image, 'products/' . $request->product_id);
            $image->product_id = $request->product_id;
            $image->color_id = $request->color_id;
            $image->save();

            $images = ProductImage::query()
                ->select('image', 'product_id', 'color_id')
                ->where('product_id', $request->product_id)
                ->where('color_id', $request->color_id)
                ->get();
            foreach ($images as $img) {
                $img->image = config('filesystems.aws_base_url') . $img->image;
            }
            return response()->json([
                'status' => true,
                'message' => trans('messages.stores.stores'),
                'data' => $images
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in addImage of seller UpdateInventory' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function addBundle(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|numeric|exists:products,id',
//                'store_id' => 'required|numeric|exists:stores,id',
                'quantity' => 'required|numeric',
                'price' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            $storeId = Store::query()->select('id')->where('user_id', $request->user_id)->first()->id;
            if (!AuthorizationHelper::isAuthorized('product_id', $request->product_id, 'store_id', $storeId, ProductStore::class)) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.general.error'),
                    'data' => ''
                ], AResponseStatusCode::FORBIDDEN);
            }
            $count = Bundle::query()
                ->where('product_id', $request->product_id)
                ->where('store_id', $storeId)
                ->get()->count();
            if ($count >= 3) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.package.update_bundles_limit'),
                    'data' => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }
            $bundle = new Bundle();
            $bundle->product_id = $request->product_id;
            $bundle->store_id = $storeId;
            $bundle->quantity = $request->quantity;
            $bundle->price = $request->price;
            $bundle->save();

            return response()->json([
                'status' => true,
                'message' => trans('messages.package.bundle_added'),
                'data' => $bundle
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in addBundle of seller UpdateInventory' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
