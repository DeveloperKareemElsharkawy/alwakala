<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\AChannels\AChannels;
use App\Enums\Activity\Activities;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\Stock\ATransactionTypes;
use App\Enums\StoreTypes\StoreType;
use App\Events\Inventory\StockMovement;
use App\Events\Logs\DashboardLogs;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Products\CreateProductSupplierStep1Request;
use App\Http\Requests\Products\CreateRetailerProductDashboardRequest;
use App\Http\Requests\Products\CreateRetailerProductRequestV2Step2;
use App\Http\Requests\Products\CreateSupplierProductRequestV2Step2;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Log\ServerError;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\Product;
use App\Models\ProductImage;
use App\Repositories\BarCodeRepository;
use App\Repositories\ColorRepository;
use App\Repositories\CreateProductRepository;
use App\Repositories\PackingUnitRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProductStoreRepository;
use App\Repositories\ProductStoreStockRepository;
use App\Repositories\StoreRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreatProductsController extends BaseController
{
    public $productsRepo;
    public $productStoreRepository;
    public $lang;

    public function __construct(ProductRepository $productRepository, ProductStoreRepository $productStoreRepository, Request $request)
    {
        $this->productsRepo = $productRepository;
        $this->productStoreRepository = $productStoreRepository;
        $this->lang = LangHelper::getDefaultLang($request);
    }

    public function CreateSupplierProductStep1(CreateProductSupplierStep1Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $where = ['store_type_id' => StoreType::SUPPLIER];
            $store = StoreRepository::getStoreByStoreId($request->store_id, $where);
            if (!$store) {
                return response()->json([
                    'status' => false,
                    'message' => 'check existence of store',
                    'data' => ''
                ], AResponseStatusCode::NOT_FOUNT);
            }
            $request->owner_id = $store->user_id;
            $request->channel = AChannels::ADMIN_DASHBOARD;
            $request->store_id = $store->id;

            $product = CreateProductRepository::createProductV2($request);

            if (!$product) {
                return response()->json([
                    'status' => false,
                    'message' => 'some thing error in product data',
                    'data' => $product
                ], AResponseStatusCode::NOT_FOUNT);
            }
            $logData['id'] = $product->id;
            $logData['ref_name_ar'] = $product->name;
            $logData['ref_name_en'] =  $product->name;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::CREATE_PRODUCT_S1;
            event(new DashboardLogs($logData, 'products'));
            return response()->json([
                'status' => true,
                'message' => 'product created',
                'data' => $product
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in CreateSupplierProductStep1 of dashboard CreatProducts' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function CreateSupplierProductStep2(CreateSupplierProductRequestV2Step2 $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();
            $userId = Product::query()
                ->select('owner_id')
                ->where('id', $request->product_id)
                ->first()->owner_id;
            $store = StoreRepository::getStoreByUserId($userId);
            $productStore = $this->productStoreRepository->getProductStore($request->product_id, $store->id);
            $colorExists = ColorRepository::checkIfColorExists($productStore->id, $request->color_id);
            if ($colorExists) {
                return response()->json([
                    'status' => false,
                    'message' => 'this color already exists',
                    'data' => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }
            $packingUnitProduct = PackingUnitRepository::packingUnitProduct($request->product_id);
            $productAttrs = ProductRepository::productAttrs($packingUnitProduct->id);
            $total_packing_unit_count = 0;
            $total_basic_unit_count = 0;
            foreach ($productAttrs as $attr) {
                $total_basic_unit_count += $attr['quantity'];
            }
            $barcodeProduct = BarCodeRepository::createBarcodeProduct($request, $store->id);
            if (!$barcodeProduct) {
                return response()->json([
                    'status' => true,
                    'message' => 'barcode is used for other product',
                    'data' => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }

            foreach ($request->images as $image) {
                 CreateProductRepository::createProductImage($image, $request);

                $total_packing_unit_count += $request->quantity;
            }
            foreach ($productAttrs as $productAttr) {
                $stock = $total_basic_unit_count * $request->quantity;
                ProductStoreStockRepository::createProductStoreStock($productStore->id, $productAttr, $request, $stock);
            }
            $totalStock = $total_basic_unit_count * $total_packing_unit_count;
            $product=Product::query()->where('id',$request->product_id)->first();
            $logData['id'] = $request->product_id;
            $logData['ref_name_ar'] = $product->name;
            $logData['ref_name_en'] =  $product->name;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::CREATE_PRODUCT_S2;
            event(new DashboardLogs($logData, 'products'));
            event(new StockMovement($totalStock, $request->product_id, ATransactionTypes::PRODUCT, $store->id));
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'product created',
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in CreateSupplierProductStep2 of dashboard CreatProducts' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function CreateRetailerProductStep1(CreateRetailerProductDashboardRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $where = ['store_type_id' => StoreType::RETAILER];
            $store = StoreRepository::getStoreByStoreId($request->store_id, $where);
            if (!$store) {
                return response()->json([
                    'status' => false,
                    'message' => 'check existence of store',
                    'data' => ''
                ], AResponseStatusCode::NOT_FOUNT);
            }

            $request->owner_id = $store->user_id;
            $request->channel = AChannels::ADMIN_DASHBOARD;
            $request->store_id = $store->id;

            $product = CreateProductRepository::createRetailerProductV2($request);
            if (!$product) {
                return response()->json([
                    'status' => false,
                    'message' => 'some thing error in product data',
                    'data' => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }
            $product=Product::query()->where('id',$request->product_id)->first();
            $logData['id'] = $product->id;
            $logData['ref_name_ar'] = $product->name;
            $logData['ref_name_en'] =  $product->name;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::CREATE_PRODUCT_S1;
            event(new DashboardLogs($logData, 'products'));
            return response()->json([
                'status' => true,
                'message' => 'product created',
                'data' => $product
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in CreateRetailerProductStep1 of dashboard CreatProducts' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function CreateRetailerProductStep2(CreateRetailerProductRequestV2Step2 $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();
            $userId = Product::query()
                ->select('owner_id')
                ->where('id', $request->product_id)
                ->first()->owner_id;
            $store = StoreRepository::getStoreByUserId($userId);
            $productStore = $this->productStoreRepository->getProductStore($request->product_id, $store->id);
            $colorExists = ColorRepository::checkIfColorExists($productStore->id, $request->color_id);

            if ($colorExists) {
                return response()->json([
                    'status' => false,
                    'message' => 'this color already exists',
                    'data' => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }
            $barcodeProduct = BarCodeRepository::createBarcodeProduct($request, $store->id);
            if (!$barcodeProduct) {
                return response()->json([
                    'status' => true,
                    'message' => 'barcode is used for other product',
                    'data' => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }
            $stock = 0;
            foreach ($request->sizes as $size) {
                $stock += $size['quantity'];
                //TODO validate duplicate data like colors on stocks, images and barcodes
                // inserted before this step
                ProductStoreStockRepository::createProductStoreStock($productStore->id, $size, $request, $size['quantity']);
            }

            foreach ($request->images as $image) {
                ProductImage::create([
                    'image' => UploadImage::uploadImageToStorage($image, 'products/' . $request->product_id),
                    'color_id' => $request->color_id,
                    'product_id' => $request->product_id
                ]);
            }
            $logData['id'] = $request->product_id;
            $logData['ref_name_ar'] = $product->name;
            $logData['ref_name_en'] =  $product->name;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::CREATE_PRODUCT_S2;
            event(new DashboardLogs($logData, 'products'));
            event(new StockMovement($stock, $request->product_id, ATransactionTypes::PRODUCT, $store->id));
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'product created',
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in CreateRetailerProductStep2 of dashboard CreatProducts' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

}
