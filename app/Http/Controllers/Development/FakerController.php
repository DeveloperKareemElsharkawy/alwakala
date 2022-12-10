<?php

namespace App\Http\Controllers\Development;

use App\Enums\AStatusCodeResponse;
use App\Lib\Log\ServerError;
use App\Models\BarcodeProduct;
use App\Models\BrandStore;
use App\Models\Bundle;
use App\Models\CategoryStore;
use App\Models\Color;
use App\Models\PackingUnit;
use App\Models\PackingUnitProduct;
use App\Models\PackingUnitProductAttribute;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductStore;
use App\Models\ProductStoreStock;
use App\Models\Seller;
use App\Models\Size;
use App\Models\Store;
use App\Models\StoreOpeningHour;
use App\Models\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FakerController extends Controller
{
    public function createSeller($count)
    {
        try {
            DB::beginTransaction();
            User::factory()->count((int)$count)->create()->each(function ($user) {

                $store = Store::factory()->create([
                    'user_id' => $user->id
                ]);

                Seller::factory()->create([
                    'store_id' => $store->id,
                    'user_id' => $user->id
                ]);
                CategoryStore::factory()->create([
                    'store_id' => $store->id
                ]);

                BrandStore::factory()->create([
                    'store_id' => $store->id
                ]);
                StoreOpeningHour::factory()->count(7)->create([
                    'store_id' => $store->id
                ]);
            });
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'seller created',
                'date' => ''
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return ServerError::handle($e);
        }
    }

    public function createProducts(Request $request, $count, $userId)
    {
        try {
            if ($request->header('Authorization') != 'Bearer QAZ_qaz_123') {
                return response()->json([
                    'status' => false,
                    'message' => 'Un Authorized'
                ], AStatusCodeResponse::UNAUTHORIZED);
            }

            DB::beginTransaction();
            $images = [
                '/images/seeders/p1.jpg',
                '/images/seeders/p2.jpg',
                '/images/seeders/p3.jpeg',
                '/images/seeders/p4.jpeg',
                '/images/seeders/p5.jpeg',
                '/images/seeders/p6.jpeg',
                '/images/seeders/p7.jpeg',
                '/images/seeders/p8.jpg',
                '/images/seeders/p9.jpg',
                '/images/seeders/p10.jpeg',
                '/images/seeders/p11.jpeg',
                '/images/seeders/p12.jpeg',
                '/images/seeders/p13.jpg',
                '/images/seeders/p14.jpg',
                '/images/seeders/p15.jpeg',
                '/images/seeders/p16.jpeg',
                '/images/seeders/p17.jpeg',
                '/images/seeders/p18.png',
                '/images/seeders/p19.jpeg',
                '/images/seeders/p20.jpeg',
                '/images/seeders/p20.jpeg',
                '/images/seeders/p21.jpeg',
            ];
            $selectedImages = array_random($images, 3);

            $sizes = Size::query()->pluck('id')->toArray();
            $selectedSizes = array_random($sizes, 4);

            $colors = Color::query()->pluck('id')->toArray();
            $selectedColors = array_random($colors, 3);

            $packingUnits = PackingUnit::query()->where('id', '!=', 1)->pluck('id')->toArray();
            $selectedPacingUnit = array_random($packingUnits);
            Product::factory()->count((int)$count)->create(['owner_id'=> (int)$userId])
                ->each(function ($product) use ($selectedColors, $selectedSizes, $selectedImages, $selectedPacingUnit) {

                    $storeId = Store::query()->select('id')->where('user_id', $product->owner_id)->first()->id;
                    $productStore = ProductStore::factory()->create([
                        'product_id' => $product->id,
                        'store_id' => $storeId
                    ]);

                    PackingUnitProduct::query()->insert([
                        [
                            'product_id' => $product->id,
                            'packing_unit_id' => 1,
                            'basic_unit_count' => count($selectedSizes),
                            'basic_unit_id' => $selectedPacingUnit
                        ], [
                            'product_id' => $product->id,
                            'packing_unit_id' => $selectedPacingUnit,
                            'basic_unit_count' => 1,
                            'basic_unit_id' => $selectedPacingUnit
                        ]
                    ]);

                    $packingUnitProductId = PackingUnitProduct::query()
                        ->where('product_id', $product->id)
                        ->where('packing_unit_id', 1)
                        ->first()->id;

                    $barcodeProduct = [];
                    $productStock = [];
                    $productImage = [];
                    $packingAttribute = [];
                    foreach ($selectedColors as $i => $color) {
                        $barcodeProduct[$i]['barcode'] = random_int(10000000, 99999999);
                        $barcodeProduct[$i]['product_id'] = $product->id;
                        $barcodeProduct[$i]['color_id'] = $color;

                        foreach ($selectedImages as $j => $image) {
                            $productImage[$j]['image'] = $image;
                            $productImage[$j]['color_id'] = $color;
                            $productImage[$j]['product_id'] = $product->id;
                        }
                        ProductImage::query()->insert($productImage);
                        foreach ($selectedSizes as $index => $size) {
                            $productStock[$index]['product_store_id'] = $productStore->id;
                            $productStock[$index]['size_id'] = $size;
                            $productStock[$index]['color_id'] = $color;
                            $productStock[$index]['stock'] = 1000;
                            $productStock[$index]['reserved_stock'] = 0;
                            $productStock[$index]['available_stock'] = 1000;
                            $productStock[$index]['sold'] = 0;
                            $productStock[$index]['returned'] = 0;

                            $packingAttribute[$index]['packing_unit_product_id'] = $packingUnitProductId;
                            $packingAttribute[$index]['size_id'] = $size;
                            $packingAttribute[$index]['quantity'] = 1;
                        }
                        ProductStoreStock::query()->insert($productStock);
                    }
                    PackingUnitProductAttribute::query()->insert($packingAttribute);
                    BarcodeProduct::query()->insert($barcodeProduct);
                });
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Products created',
                'date' => ''
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return ServerError::handle($e);
        }
    }
}
