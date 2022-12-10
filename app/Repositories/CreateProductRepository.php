<?php


namespace App\Repositories;


use App\Enums\DiscountTypes\DiscountTypes;
use App\Http\Controllers\Controller;
use App\Lib\Log\ServerError;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\Category;
use App\Models\PackingUnit;
use App\Models\PackingUnitProduct;
use App\Models\PackingUnitProductAttribute;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductStore;
use App\Models\ProductStoreStock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class CreateProductRepository extends Controller
{

    public static function createProductV2(Request $request)
    {
        try {
            DB::beginTransaction();
            $product = new Product();
            $product->name = $request->name;
            $product->description = $request->description;
            $product->activation = isset($request->activation) && !is_null($request->activation) ? $request->activation : 1;
            $product->category_id = $request->category_id;
            $product->brand_id = $request->brand_id;
            $product->owner_id = $request->owner_id;
            $product->channel = $request->channel;

            if ($request->policy_id == 1) {
                $product->consumer_price = $request->consumer_price;
            }

            $product->unit_id = $request->unit_id;
            $product->material_id = $request->material_id;
            $product->material_rate = $request->material_rate;
            $product->shipping_method_id = $request->shipping_method_id;
            $product->policy_id = $request->policy_id;
            $product->save();

            $basicUnit = Category::query()
                ->select('packing_unit_id')
                ->where('id', $request->category_id)
                ->first();
            if (!$basicUnit) {
                return false;
            }
//            $min_bundle_price = 999999999;
//            foreach ($request->bundles as $bundle) {
//                if ($bundle['price'] < $min_bundle_price) {
//                    $min_bundle_price = $bundle['price'];
//                }
//                Bundle::query()->create([
//                    'product_id' => $product->id,
//                    'store_id' => $request->store_id,
//                    'quantity' => $bundle['quantity'],
//                    'price' => $bundle['price']
//                ]);
//            }
            $generatedQRCode = 'PS-' . mt_rand(1000000, 9999999); //  generate qrcode

            $image = \QrCode::format('png')
                ->size(200)->errorCorrection('H')
                ->generate($generatedQRCode); //   generate qrcode image

            $uploadedImage = UploadImage::uploadSVGToStorage($image); // upload qrcode image to s3 storage

            ProductStore::create([
                'product_id' => $product->id,
                'store_id' => $request->store_id,
                'views' => 0,
                'publish_app_at' => $request->publish_app_at,
                'price' => $request->price,
                'free_shipping' => $request->free_shipping,
                'net_price' => $request->price - (($request->discount / 100) * $request->price),
                'discount' => $request->discount,
                'discount_type' => DiscountTypes::PERCENTAGE,
                'barcode' => $uploadedImage,
                'barcode_text' => $generatedQRCode,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            $package_packing_unit = PackingUnitProduct::create([
                'product_id' => $product->id,
                'packing_unit_id' => 1,
                'basic_unit_count' => 1,
                'basic_unit_id' => $basicUnit->packing_unit_id
            ]);
            // PackingUnitProduct::create([
            //     'product_id' => $product->id,
            //     'packing_unit_id' => $basicUnit->packing_unit_id,
            //     'basic_unit_count' => 1,
            //     'basic_unit_id' => $basicUnit->packing_unit_id
            // ]);

            $total_basic_unit_count = 0;
            foreach ($request->product_attributes as $attribute) {
                PackingUnitProductAttribute::create([
                    'packing_unit_product_id' => $package_packing_unit->id,
                    'size_id' => $attribute['size_id'],
                    'quantity' => $attribute['quantity']
                ]);
                $total_basic_unit_count += $attribute['quantity'];
            }
            PackingUnitProduct::query()
                ->where('packing_unit_id', 1)
                ->where('product_id', $product->id)
                ->update(['basic_unit_count' => $total_basic_unit_count]);
            DB::commit();
            return $product;
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return ServerError::handle($e);
        }
    }

    public static function createRetailerProductV2(Request $request)
    {
        try {
            DB::beginTransaction();
            $product = new Product();
            $product->name = $request->name;
            $product->description = $request->description;
            $product->activation = isset($request->activation) && !is_null($request->activation) ? $request->activation : 1;
            $product->category_id = $request->category_id;
            $product->brand_id = $request->brand_id;
            $product->owner_id = $request->owner_id;
            $product->channel = $request->channel;
            $product->unit_id = $request->unit_id;
            $product->material_id = $request->material_id;
            $product->material_rate = $request->material_rate;
            $product->shipping_method_id = $request->shipping_method_id;

            if ($request->policy_id == 1) {
                $product->consumer_price = $request->consumer_price;
            }

            $product->policy_id = 2;
            $product->save();
            $basicUnit = Category::query()
                ->where('id', $request->category_id)
                ->first();
            if (!$basicUnit) {
                return false;
            }

            $generatedQRCode = 'PS-' . mt_rand(1000000, 9999999); //  generate qrcode

            $image = \QrCode::format('png')
                ->size(200)->errorCorrection('H')
                ->generate($generatedQRCode); //   generate qrcode image

            $uploadedImage = UploadImage::uploadSVGToStorage($image); // upload qrcode image to s3 storage

            ProductStore::create([
                'product_id' => $product->id,
                'store_id' => $request->store_id,
                'views' => 0,
                'publish_app_at' => $request->publish_app_at,
                'discount_type' => DiscountTypes::PERCENTAGE,
                'barcode' => $uploadedImage,
                'barcode_text' => $generatedQRCode,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            PackingUnitProduct::create([
                'product_id' => $product->id,
                'packing_unit_id' => $basicUnit->packing_unit_id,
                'basic_unit_count' => 1,
                'basic_unit_id' => $basicUnit->packing_unit_id
            ]);

            DB::commit();
            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            return ServerError::handle($e);
        }
    }

    public static function cloneProduct(Request $request, $product, $productStocks)
    {
        try {
            DB::beginTransaction();

            $store = StoreRepository::getStoreByUserId($request->user_id);


            $generatedQRCode = 'PS-' . mt_rand(1000000, 9999999); //  generate qrcode

            $image = \QrCode::format('png')
                ->size(200)->errorCorrection('H')
                ->generate($generatedQRCode); //   generate qrcode image

            $uploadedImage = UploadImage::uploadSVGToStorage($image); // upload qrcode image to s3 storage


            $productStore = ProductStore::create([
                'product_id' => $product->id,
                'store_id' => $store->id,
                'views' => 0,
                'free_shipping' => $request->free_shipping,
                'publish_app_at' => '2020-10-18',
                'price' => $request->price,
                'show_to_consumer' => true,
                'net_price' => $request->price - (($request->discount / 100) * $request->price),
                'discount' => $request->discount,
                'barcode' => $uploadedImage,
                'barcode_text' => $generatedQRCode,
                'discount_type' => DiscountTypes::PERCENTAGE,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);


            foreach ($productStocks as $productStock) {
                ProductStoreStock::query()
                    ->create([
                        'product_store_id' => $productStore->id,
                        'size_id' => $productStock['size_id'],
                        'color_id' => $productStock['color_id'],
                        'stock' => $productStock['stock'],
                        'reserved_stock' => 0,
                        'available_stock' => $productStock['available_stock'],
                        'sold' => 0,
                        'returned' => 0,
                        'approved' => true,
                    ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return ServerError::handle($e);
        }
    }

    public static function createProductImage($image, $request)
    {
        ProductImage::create([
            'image' => UploadImage::uploadImageToStorage($image, 'products/' . $request->product_id),
            'color_id' => $request->color_id,
            'product_id' => $request->product_id
        ]);
    }

}
