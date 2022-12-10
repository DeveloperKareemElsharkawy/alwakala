<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\Activity\Activities;
use App\Enums\DiscountTypes\DiscountTypes;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Events\Logs\DashboardLogs;
use App\Http\Controllers\BaseController;
use App\Lib\Helpers\Authorization\AuthorizationHelper;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Models\Bundle;
use App\Models\Product;
use App\Models\ProductStore;
use App\Models\ProductStoreStock;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PackingUnitProduct;
use App\Models\PackingUnitProductAttribute;
use App\Repositories\UpdateProductRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UpdateProductsController extends BaseController
{
    public function updateInfo(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:products,id',
                'name' => 'required|string|max:255',
                'description' => 'required|string|max:255',
                'brand_id' => 'nullable|exists:brands,id',
                'policy_id' => 'nullable|exists:policies,id',
                'shipping_method_id' => 'nullable|exists:shipping_methods,id',
                'material_id' => 'nullable|exists:materials,id',
                'category_id' => 'required|exists:categories,id',
                'consumer_price' => 'nullable|numeric',
                'activation' => 'required',
                // 'free_shipping' => 'required',
                // 'price' => 'required',
                // 'discount' => 'required',
                // 'publish_app_at' => 'required',
                'product_attributes' => 'required|array',
                'product_attributes.*.size_id' => 'required|numeric|exists:sizes,id',
                'product_attributes.*.quantity' => 'required',
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            UpdateProductRepository::updateProductSupplier($request);
            return response()->json([
                'success' => true,
                'message' => 'info updated',
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in updateInfo of dashboard UpdateProducts' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }
    }

    public function updateStoreDetails(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:product_store,id',
                'publish_app_at' => 'required|string|max:255',
                'price' => 'required|numeric',
                'net_price' => 'required|numeric',
                'discount' => 'required|numeric',
                'discount_type' => 'required|in:' . DiscountTypes::AMOUNT . ',' . DiscountTypes::PERCENTAGE,
                'free_shipping' => 'required|boolean',
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            $product = ProductStore::query()->find($request->id);
            $product->publish_app_at = $request->publish_app_at;
            $product->price = $request->price;
            $product->net_price = $request->net_price;
            $product->discount = $request->discount;
            $product->discount_type = $request->discount_type;
            $product->free_shipping = $request->free_shipping;
            $product->save();
            $productData = Product::query()->where('id', $product->product_id)->first();
            $logData['id'] = $request->id;
            $logData['ref_name_ar'] = $productData->name;
            $logData['ref_name_en'] = $productData->name;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::UPDATE_PRODUCT_DETAILS;
            event(new DashboardLogs($logData, 'products'));
            return response()->json([
                'success' => true,
                'message' => 'store details updated',
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in updateStoreDetails of dashboard UpdateProducts' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function updateProductStock(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:product_store_stock,id',
                'stock' => 'required|numeric',
                'reserved_stock' => 'required|numeric',
                'available_stock' => 'required|numeric',
                'sold' => 'required|numeric'
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            $product = ProductStoreStock::query()->find($request->id);

            if ($request->reserved_stock + $request->available_stock + $request->sold != $request->stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error in values',
                    'data' => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }

            $product->stock = $request->stock;
            $product->reserved_stock = $request->reserved_stock;
            $product->available_stock = $request->available_stock;
            $product->sold = $request->sold;
            $product->save();
            $productStore = ProductStore::query()->find($product->product_store_id);
            $productData = Product::query()->where('id', $productStore->product_id)->first();
            $logData['id'] = $request->id;
            $logData['ref_name_ar'] = $productData->name;
            $logData['ref_name_en'] = $productData->name;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::UPDATE_PRODUCT_STOCK;
            event(new DashboardLogs($logData, 'products'));
            return response()->json([
                'success' => true,
                'message' => 'store details updated',
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in updateProductStock of dashboard UpdateProducts' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function updateProductBundle(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:bundles,id',
                'quantity' => 'required|numeric',
                'price' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            $bundle = Bundle::query()->find($request->id);

            $bundle->quantity = $request->quantity;
            $bundle->price = $request->price;
            $bundle->save();
            $product = Product::query()->where('id', $bundle->product_id)->first();
            $logData['id'] = $bundle->product_id;
            $logData['ref_name_ar'] = $product->name;
            $logData['ref_name_en'] = $product->name;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::UPDATE_PRODUCT_BUNDLE;
            event(new DashboardLogs($logData, 'products'));
            return response()->json([
                'success' => true,
                'message' => 'Product bundle updated',
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in updateProductBundle of dashboard UpdateProducts' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
