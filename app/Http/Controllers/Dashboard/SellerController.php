<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\Activity\Activities;
use App\Enums\AStatusCodeResponse;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\StoreTypes\StoreType;
use App\Enums\UserTypes\UserType;
use App\Events\Logs\DashboardLogs;
use App\Events\Users\ApprovePendingSeller;
use App\Exports\Dashboard\SellersExport;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Consumer\EmailRequest;
use App\Http\Resources\Dashboard\SellerResource;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Mail\Auth\PasswordRequestReset;
use App\Mail\SendMail;
use App\Models\Product;
use App\Models\Seller;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class SellerController extends BaseController
{
    public function index(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'activation' => 'required'
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            $query = User::query()
                ->select('id', 'name', 'email', 'mobile', 'activation','created_at')
                ->orderBy('updated_at', 'desc');
            $offset = $request->query('offset') ? $request->query('offset') : 0;
            $limit = $request->query('limit') ? $request->query('limit') : config('dashboard.pagination_limit');
            if ($request->filled("id")) {
                $query->where('id', intval($request->id));
            }
            if ($request->filled("name")) {
                $searchQuery = "%" . $request->get("name") . "%";
                $query->where('name', "ilike", $searchQuery);
            }
            if ($request->filled("email")) {
                $searchQuery = "%" . $request->get("email") . "%";
                $query->where('email', "ilike", $searchQuery);
            }
            if ($request->filled("mobile")) {
                $searchQuery = "%" . $request->get("mobile") . "%";
                $query->where('mobile', "ilike", $searchQuery);
            }
            if ($request->filled("activation")) {
                $query->where('activation', $request->activation);
            }
            if ($request->filled("sort_by_id")) {
                $query->orderBy('id', $request->sort_by_id);
            }
            if ($request->filled("sort_by_name")) {
                $query->orderBy('name', $request->sort_by_name);
            }
            if ($request->filled("sort_by_email")) {
                $query->orderBy('email', $request->sort_by_email);
            }
            if ($request->filled("sort_by_mobile")) {

                $query->orderBy('mobile', $request->sort_by_mobile);
            }
            if ($request->filled("sort_by_activation")) {
                $query->orderBy('activation', $request->sort_by_activation);
            }
            $query->where('type_id', UserType::SELLER);
            $count = $query->count();
            $sellers = $query->limit($limit)->offset($offset)->get();

            foreach ($sellers as $seller) {
                if ($seller->image)
                    $seller->image = config('filesystems.aws_base_url') . $seller->image;
            }
            return response()->json([
                "status" => true,
                "message" => "sellers retrieved successfully",
                "data" => $sellers,
                'offset' => (int)$offset,
                'limit' => (int)$limit,
                'total' => $count,
            ], AStatusCodeResponse::SUCCESSFUL);
        } catch (\Exception $e) {
            Log::error('error in index of dashboard seller' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function show($id)
    {
        try {
            $seller = User::query()
                ->select(['users.id', 'users.created_at as register_date', 'users.name', 'users.mobile', 'users.email', 'cities.name_en as city_name', 'addresses.address'])
                ->leftJoin('addresses', 'addresses.user_id', '=', 'users.id')
                ->leftJoin('cities', 'cities.id', '=', 'addresses.city_id')
                ->where('users.id', $id)->first();
            if ($seller === null) {
                return response()->json([
                    "status" => false,
                    "message" => "seller id not valid",
                    "data" => ''
                ], AStatusCodeResponse::BAD_REQUEST);
            }

            if ($seller->image)
                $seller->image = config('filesystems.aws_base_url') . $seller->image;

            return response()->json([
                "status" => true,
                "message" => "seller retrieved successfully",
                "data" => $seller
            ], AStatusCodeResponse::SUCCESSFUL);
        } catch (\Exception $e) {
            Log::error('error in show of dashboard seller' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|numeric|exists:users,id',
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email,' . $request->id,
                'mobile' => 'required|min:11|max:11|unique:users,mobile,' . $request->id,
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            $seller = User::query()->find($request->id);
            $seller->name = $request->name;
            if ($request->filled('password')) {
                $seller->password = $request->password;
            }
            $seller->email = $request->email;
            $seller->mobile = $request->mobile;
            if($request->password){
                $seller->password = bcrypt($request->password);
            }

            if ($request->hasFile('image')) {
                Storage::disk('s3')->delete($seller->image);
                $seller->image = UploadImage::uploadImageToStorage($request->image, 'sellers');
            }
            $seller->save();
            $logData['id'] = $seller->id;
            $logData['ref_name_ar'] = $seller->name;
            $logData['ref_name_en'] =  $seller->name;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::UPDATE_SELLER;
            event(new DashboardLogs($logData, 'sellers'));
            return response()->json([
                'status' => true,
                'message' => 'seller Updated',
                'data' => $seller
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in update of dashboard seller' . __LINE__ . $e);
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

            $seller = User::query()->find($request->id);
            Storage::disk('s3')->delete($seller->image);
            $logData['id'] = $seller->id;
            $logData['ref_name_ar'] = $seller->name;
            $logData['ref_name_en'] =  $seller->name;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::DELETE_SELLER;
            event(new DashboardLogs($logData, 'sellers'));
            $seller->delete();
            return response()->json([
                'status' => true,
                'message' => 'seller deleted',
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in delete of dashboard seller' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getSellers(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'activation' => 'required'
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            $sellers = User::query()->where('activation', '=', (boolean)$request->activation)
                ->where('type_id', '=', UserType::SELLER)
                ->select(['id', 'name', 'email', 'mobile', 'activation'])
                ->get();

            return response()->json([
                'message' => '',
                'data' => $sellers
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in getSellers of dashboard seller' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

    public function getSellerDetails(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'seller_id' => 'required|exists:users,id'
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            $sellerDetails = User::query()->where('users.id', (int)$request->seller_id)
                ->where('users.type_id', '=', UserType::SELLER)
                ->join('stores', 'users.id', '=', 'stores.user_id')
                ->join('store_types', 'stores.store_type_id', '=', 'store_types.id')
                ->join('cities', 'stores.city_id', '=', 'cities.id')
                ->select([
                    'users.name as seller_name', 'users.email', 'users.mobile', 'users.activation',
                    'stores.name as store_name', 'stores.landing_number',
                    'stores.image', 'stores.logo', 'stores.licence',
                    'stores.latitude', 'stores.longitude',
                    'stores.is_store_has_delivery', 'stores.is_brand',
                    'store_types.name_ar as store_type_name_ar', 'store_types.name_en as store_type_name_en',
                    'cities.name_ar as city_name_ar', 'cities.name_en as city_name_en'
                ])->first();

            $sellerDetails->image = config('filesystems.aws_base_url') . $sellerDetails->image;
            $sellerDetails->logo = config('filesystems.aws_base_url') . $sellerDetails->logo;
            $sellerDetails->licence = config('filesystems.aws_base_url') . $sellerDetails->licence;

            return response()->json([
                'message' => '',
                'data' => $sellerDetails
            ], AResponseStatusCode::SUCCESS);


        } catch (\Exception $e) {
            Log::error('error in getSellerDetails of dashboard seller' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function approvePendingSeller(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'seller_id' => 'required|exists:users,id'
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            DB::beginTransaction();
            User::query()->where('id', $request->seller_id)
                ->update(['activation' => true]);
            Store::query()->where('user_id', $request->seller_id)->update(['is_verified' => true]);
            $seller = User::query()->where('id', $request->seller_id)->first();
            event(new ApprovePendingSeller([$request->seller_id]));
            $data['subject'] = "Welcome";
            $data['message'] = "Welcome to elwekala ";
            $data['user_name'] = $seller->name;
            Mail::to($request->email)->send(new SendMail($data));
            $logData['id'] = $request->seller_id;
            $logData['ref_name_en'] = $seller->name;
            $logData['ref_name_ar'] = $seller->name;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::APPROVE_SELLER;
            event(new DashboardLogs($logData, 'sellers'));
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Seller Approved Successfully',
                'data' => ''
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in approvePendingSeller of dashboard seller' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getForSelection(Request $request)
    {
        try {

            $query = Seller::query()
                ->select('users.id', 'stores.name as name', 'stores.id as store_id')
                ->join('users', 'users.id', '=', 'sellers.user_id')
                ->Join('stores', 'stores.user_id', '=', 'users.id')
                ->where('users.type_id', UserType::SELLER);
            if ($request->filled('type_id')){
                $query->where('stores.store_type_id', $request->type_id);
            }
            if ($request->filled('category')){
                $query->leftJoin('category_store', 'category_store.store_id', '=', 'stores.id');
                $query->whereIn('category_store.category_id', explode(',',$request->query('category')));
            }
            if ($request->filled('name')){
                $query->where('users.name', "like", "%".$request->query('name')."%");
            }
            $data = $query->get();
            return response()->json([
                "status" => AResponseStatusCode::SUCCESS,
                "message" => "data retrieved successfully",
                "data" => $data
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getForSelection of dashboard seller' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }

    }

    public function export(Request $request)
    {
        try {
            return  Excel::download(new SellersExport($request), 'sellers.xlsx');
        } catch (\Exception $e) {
            Log::error('error in Seller in dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function sendEmail(EmailRequest $request){
        try {
            $data['subject'] = $request->subject;
            $data['message'] = $request->message;
            $data['user_name'] = User::query()->where('email',$request->email)->first()->name;
            Mail::to($request->email)->send(new SendMail($data));
            return response()->json([
                "status" => AResponseStatusCode::SUCCESS,
                "message" => "email sent successfully",
                "data" => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in send email of dashboard seller' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function unactiveSeller(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'seller_id' => 'required|exists:users,id'
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            DB::beginTransaction();
            User::query()->where('id', $request->seller_id)
                ->update(['activation' => false]);
            Store::query()->where('user_id', $request->seller_id)->update(['is_verified' => false]);
            Product::query()->where('owner_id', $request->seller_id)->update(['activation' => false]);
            $seller = User::query()->where('id', $request->seller_id)->first();
            $logData['id'] = $request->seller_id;
            $logData['ref_name_en'] = $seller->name;
            $logData['ref_name_ar'] = $seller->name;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::UNACTIVE_SELLER;
            event(new DashboardLogs($logData, 'sellers'));
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Seller Approved Successfully',
                'data' => ''
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in approvePendingSeller of dashboard seller' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
