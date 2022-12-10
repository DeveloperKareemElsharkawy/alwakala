<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\Activity\Activities;
use App\Enums\Activity\ActivityType;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\Stock\ATransactionTypes;
use App\Enums\StoreTypes\StoreType;
use App\Events\Inventory\StockMovement;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Products\CreateProductByBarcodeRequest;
use App\Http\Requests\Products\CreateRetailerProductRequestV2;
use App\Http\Requests\Products\CreateRetailerProductRequestV2Step2;
use App\Http\Requests\Products\CreateSupplierProductRequestV2;
use App\Http\Requests\Products\CreateSupplierProductRequestV2Step2;
use App\Lib\Helpers\Categories\CategoryHelper;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Log\ValidationError;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\BarcodeProduct;
use App\Models\CategorySize;
use App\Models\Color;
use App\Models\PackingUnitProduct;
use App\Models\PackingUnitProductAttribute;
use App\Models\Product;
use App\Models\ProductImage;
use App\Repositories\ActivitiesRepository;
use App\Repositories\BarCodeRepository;
use App\Repositories\ColorRepository;
use App\Repositories\CreateProductRepository;
use App\Repositories\PackingUnitRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProductStoreRepository;
use App\Repositories\ProductStoreStockRepository;
use App\Repositories\StoreRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CreateProductsController extends BaseController
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


    public function storeSupplierProductV2(CreateSupplierProductRequestV2 $request)
    {
        try {

            dd($request->all());
            $validate=$this->validateData($request);
            if(!is_bool($validate)){
                return $validate;
            }
            // if (!CategoryHelper::checkCategoryLevel('sub_sub_category', $request->category_id)) {
            //     return response()->json([
            //         'status' => false,
            //         'message' => trans('messages.category.un_valid_parent'),
            //         'data' => ''
            //     ], AResponseStatusCode::BAD_REQUEST);
            // }
            $where = ['store_type_id' => StoreType::SUPPLIER];
            $store = StoreRepository::getStoreByUserId($request->seller_id, $where);
            if (!$store)
                return $this->notFound();

//            if (count($request->bundles) > 3) {
//                return response()->json([
//                    "status" => false,
//                    "message" => 'max bundle length must be less than 4',
//                    "data" => [],
//                ], AResponseStatusCode::BAD_REQUEST);
//            }

            $request->owner_id = $request->seller_id;
            $request->channel = 'seller-app';
            $request->store_id = $store->id;
            $request->material_rate = $request->material_rate?$request->material_rate:1;

            $product = CreateProductRepository::createProductV2($request);

            if (!$product)
                return $this->notFound();

            return $this->created(['message' => trans('messages.product.created'), 'data' => $product]);

        } catch (\Exception $e) {
            Log::error('error in storeSupplierProductV2 of seller CreateProducts' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function storeSupplierProductV2Step2(CreateSupplierProductRequestV2Step2 $request)
    {
        try {
            $validate=$this->validateData($request);
            if(!is_bool($validate)){
                return $validate;
            }
            DB::beginTransaction();
            $where = ['store_type_id' => StoreType::SUPPLIER];
            $store = StoreRepository::getStoreByUserId($request->seller_id, $where);
            $productStore = $this->productStoreRepository->getProductStore($request->product_id, $store->id);
            $colorExists = ColorRepository::checkIfColorExists($productStore->id, $request->color_id);

            if ($colorExists)
                return $this->error(['message' => trans('messages.colors.exists')]);

            $packingUnitProduct = PackingUnitRepository::packingUnitProduct($request->product_id);
            $productAttrs = ProductRepository::productAttrs($packingUnitProduct->id);
            $total_packing_unit_count = 0;
            $total_basic_unit_count = 0;
            foreach ($productAttrs as $attr) {
                $total_basic_unit_count += $attr['quantity'];
            }
            $barcodeProduct = BarCodeRepository::createBarcodeProduct($request, $store->id);

            if($request->reviewed)
                Product::query()->where('id', $request->product_id)
                ->update(['reviewed' => true]);

            if (!$barcodeProduct)
                return $this->error(['message' => trans('messages.product.barcode_used')]);

            foreach ($request->images as $image) {
                CreateProductRepository::createProductImage($image, $request);
                $total_packing_unit_count += $request->quantity;
            }
            // foreach ($productAttrs as $productAttr) {
                $stock = $request->quantity;//$total_basic_unit_count * $request->quantity;
                ProductStoreStockRepository::createProductStoreStock($productStore->id, null, $request, $stock);
            // }
            $totalStock = $total_basic_unit_count * $total_packing_unit_count;
            event(new StockMovement($totalStock, $request->product_id, ATransactionTypes::PRODUCT, $store->id));
            $data['ref_id'] = $request->product_id;
            $data['user_id'] = $request->seller_id;
            $data['action'] = Activities::CREATE_PRODUCT_S2;
            $data['type'] = ActivityType::PRODUCT;
            ActivitiesRepository::log($data);
            DB::commit();

            return $this->created(['message' => trans('messages.actions.product_added')]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in storeSupplierProductV2Step2 of seller CreateProducts' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function storeRetailerProductV2(CreateRetailerProductRequestV2 $request)
    {
        try {
            $validate=$this->validateData($request);
            if(!is_bool($validate)){
                return $validate;
            }
            if (!$request->category_id || !CategoryHelper::checkCategoryLevel('sub_sub_category', $request->category_id)) {
                return $this->error(['message' => trans('messages.category.un_valid_parent')]);
            }

            $where = ['store_type_id' => StoreType::RETAILER];
            $store = StoreRepository::getStoreByUserId($request->seller_id, $where);

            if (!$store)
                return $this->error(['message' => trans('messages.general.store_exists')]);

            $request->owner_id = $request->seller_id;
            $request->channel = 'seller-app';
            $request->store_id = $store->id;

            $product = CreateProductRepository::createRetailerProductV2($request);

            if (!$product)
                return $this->error(['message' => trans('messages.general.error')]);

            return $this->created(['message' => trans('messages.product.created'), 'data' => $product]);

        } catch (\Exception $e) {
            Log::error('error in storeRetailerProductV2 of seller CreateProducts' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function storeRetailerProductV2Step2(CreateRetailerProductRequestV2Step2 $request)
    {
        try {
            $validate=$this->validateData($request);
            if(!is_bool($validate)){
                return $validate;
            }
            DB::beginTransaction();
            $where = ['store_type_id' => StoreType::RETAILER];
            $store = StoreRepository::getStoreByUserId($request->seller_id, $where);
            $productStore = $this->productStoreRepository->getProductStore($request->product_id, $store->id);
            $colorExists = ColorRepository::checkIfColorExists($productStore->id, $request->color_id);

            if ($colorExists)
                return $this->error(['message' => trans('messages.colors.exists')]);

            $barcodeProduct = BarCodeRepository::createBarcodeProduct($request, $store->id);
            if (!$barcodeProduct)
                return $this->error(['message' => trans('messages.product.barcode_used')]);

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

            event(new StockMovement($stock, $request->product_id, ATransactionTypes::PRODUCT, $store->id));
            DB::commit();

            return $this->created(['message' => trans('messages.product.created')]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in storeRetailerProductV2Step2 of seller CreateProducts' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    private function validateData($request){
        $validator = Validator::make($request->all(), [
            'seller_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return ValidationError::handle($validator);
        }
        return true;
    }

}
