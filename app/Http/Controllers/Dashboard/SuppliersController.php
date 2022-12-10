<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\Activity\Activities;
use App\Enums\AStatusCodeResponse;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\StoreTypes\StoreType;
use App\Enums\UserTypes\UserType;
use App\Events\Logs\DashboardLogs;
use App\Http\Controllers\BaseController;
use App\Http\Resources\Dashboard\SellerResource;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\Seller;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SuppliersController extends BaseController
{
    public function index(Request $request)
    {
        try {
            $query = Seller::query()
                ->select('users.id', 'users.name', 'users.image', 'users.email', 'users.mobile', 'sellers.user_id', 'stores.seller_id')
                ->join('users', 'users.id', '=', 'sellers.user_id')
                ->Join('stores', 'stores.seller_id', '=', 'sellers.id')
                ->where('users.type_id', UserType::SELLER)
                ->where('stores.store_type_id', StoreType::SUPPLIER);
            if ($request->filled("query")) {
                $searchQuery = "%" . $request->get("query") . "%";
                $query->where('users.name', "ilike", $searchQuery)
                    ->orWhere('users.mobile', "ilike", $searchQuery);
            }
            if ($request->filled('category')) {
                $query->where('stores.category_id', intval($request->category));
            }
            if ($request->filled('state')) {
                $query->where('stores.state_id', intval($request->state));
            }
            if ($request->filled('city')) {
                $query->where('stores.city_id', intval($request->city));
            }
            if ($request->filled('sort_by_category')) {
                $query->orderBy('stores.category_id', $request->sort_by_category);
            }
            if ($request->filled('sort_by_state')) {
                $query->orderBy('stores.state_id', $request->sort_by_category);
            }
            if ($request->filled('sort_by_city')) {
                $query->orderBy('stores.city_id', $request->sort_by_category);
            }
            $supplier = $query->get();
            $response = SellerResource::collection($supplier);
            return response()->json([
                "status" => true,
                "message" => "suppliers retrieved successfully",
                "data" => $response
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in index of dashboard Suppliers' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function show($id)
    {
        try {
            $user = User::query()
                ->where('id', '=', $id)->first();
            if ($user === null) {
                return response()->json([
                    "status" => false,
                    "message" => "supplier id not valid",
                    "data" => ''
                ], AStatusCodeResponse::BAD_REQUEST);
            }
            $supplier = User::query()
                ->with('seller')
                ->where('id', $id)
                ->first();
            $supplier->image = config('filesystems.aws_base_url') . $supplier->image;

            $supplier->stores = Store::query()
                ->select('id', 'name', 'mobile', 'is_main_branch', 'is_store_has_delivery', 'city_id')
                ->with('city')
                ->where('seller_id', $supplier->seller->id)
                ->get();

            return response()->json([
                "status" => true,
                "message" => "supplier retrieved successfully",
                "data" => $supplier
            ], AStatusCodeResponse::SUCCESSFUL);
        } catch (\Exception $e) {
            Log::error('error in show of dashboard Suppliers' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|numeric|exists:users,id',
                'name' => 'required|string',
                'email' => 'required|email',
                'mobile' => 'required|min:11|max:11',
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            $supplier = User::query()->find($request->id);
            $supplier->name = $request->name;
            if ($request->filled('password')) {
                $supplier->password = $request->password;
            }
            $supplier->email = $request->email;
            $supplier->mobile = $request->mobile;
            if ($request->hasFile('image')) {
                Storage::disk('s3')->delete($supplier->image);
                $supplier->image = UploadImage::uploadImageToStorage($request->image, 'sellers');
            }
            $supplier->save();
            $logData['id'] = $supplier->id;
            $logData['ref_name_ar'] = $supplier->name;
            $logData['ref_name_en'] =  $supplier->name;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::UPDATE_SUPPLIER;
            event(new DashboardLogs($logData, 'suppliers'));
            return response()->json([
                'status' => true,
                'message' => 'supplier Updated',
                'data' => $supplier
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in update of dashboard Suppliers' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function delete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|numeric|exists:users,id',
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            $supplier = User::query()->find($request->id);
            Storage::disk('s3')->delete($supplier->image);
            $logData['id'] = $supplier->id;
            $logData['ref_name_ar'] = $supplier->name;
            $logData['ref_name_en'] =  $supplier->name;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::DELETE_SUPPLIER;
            event(new DashboardLogs($logData, 'suppliers'));
            $supplier->delete();
            return response()->json([
                'status' => true,
                'message' => 'supplier deleted',
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in delete of dashboard Suppliers' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

}
