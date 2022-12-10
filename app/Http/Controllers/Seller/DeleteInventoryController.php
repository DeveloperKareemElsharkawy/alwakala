<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Lib\Log\ServerError;
use App\Models\BarcodeProduct;
use App\Models\Bundle;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Lib\Log\ValidationError;
use App\Http\Requests\Inventory\UpdateInfoRequest;
use App\Http\Controllers\Controller;

class DeleteInventoryController extends BaseController
{

    // TODO add authorization helper

    public function deleteBundlePrice($id)
    {
        try {
            $price = Bundle::query()
                ->where('id', $id)
                ->first();
            if (!$price) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.inventory.delete_bundle_price'),
                    'data' => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }
            if ($price->delete()) {
                return response()->json([
                    'status' => true,
                    'message' => trans('messages.inventory.bundle_price_deleted'),
                    'data' => ''
                ], AResponseStatusCode::SUCCESS);
            }
            return response()->json([
                'status' => false,
                'message' => trans('messages.inventory.bundle_price_not_found'),
                'data' => ''
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in deleteBundlePrice of seller DeleteInventory' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function deleteImage($id)
    {
        try {
            $image = ProductImage::query()
                ->where('id', $id)
                ->first();
            if (!$image) {
                return response()->json([
                    'status' => false,
                    'message' =>  trans('messages.inventory.bundle_price_not_found'),
                    'data' => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }
            $imageCount = ProductImage::query()
                ->where('color_id', $image->color_id)
                ->where('product_id', $image->product_id)
                ->get()->count();
            if ($imageCount == 1) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.inventory.product_minimum_image_limit'),
                    'data' => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }
            if ($image->delete()) {
                Storage::disk('s3')->delete($image->image);
                return response()->json([
                    'status' => true,
                    'message' => trans('messages.inventory.image_deleted'),
                    'data' => ''
                ], AResponseStatusCode::SUCCESS);
            }
            return response()->json([
                'status' => false,
                'message' =>  trans('messages.inventory.bundle_price_not_found'),
                'data' => ''
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in deleteImage of seller DeleteInventory' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
