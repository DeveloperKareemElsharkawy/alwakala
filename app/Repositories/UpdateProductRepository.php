<?php


namespace App\Repositories;

use App\Enums\Activity\Activities;
use App\Enums\DiscountTypes\DiscountTypes;
use App\Events\Logs\DashboardLogs;
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


class UpdateProductRepository extends Controller
{

    public static function updateProductSupplier(Request $request)
    {
        try {
            DB::beginTransaction();
            $product = Product::query()->find($request->id);
            $product->name = $request->name;
            $product->brand_id = $request->brand_id;
            $product->category_id = $request->category_id;
            $product->policy_id = $request->policy_id;
            $product->shipping_method_id = $request->shipping_method_id;
            $product->material_id = $request->material_id;
            $product->consumer_price = $request->consumer_price;
            $product->description = $request->description;
            $product->save();
            $product->productStore->price= $request->price;
            $product->productStore->free_shipping= $request->free_shipping;
            $product->productStore->publish_app_at= $request->publish_app_at;
            $product->productStore->discount= $request->discount;
            $product->productStore->net_price= $request->price - (($request->discount / 100) * $request->price);
            $product->productStore->save();
            $total_basic_unit_count = 0;
            $attribute=[];
            foreach ($request->product_attributes as $att) {
                $attribute[]=[
                    'packing_unit_product_id' => $product->packingUnitProduct->id,
                    'size_id' => $att['size_id'],
                    'quantity' => $att['quantity']
                ];
                $total_basic_unit_count += $att['quantity'];
            }
            $product->packingUnitProduct->attributes()->delete();
            $product->packingUnitProduct->attributes()->createMany($attribute);
            $product->packingUnitProduct->basic_unit_count = $total_basic_unit_count;
            $product->packingUnitProduct->save();
            $logData['id'] = $request->id;
            $logData['ref_name_ar'] = $product->name;
            $logData['ref_name_en'] = $product->name;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::UPDATE_PRODUCT_INFO;
            event(new DashboardLogs($logData, 'products'));
            DB::commit();
            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            return ServerError::handle($e);
        }
    }

}
