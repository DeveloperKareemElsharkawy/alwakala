<?php

namespace App\Http\Controllers\Dashboard;

use App\Events\Logs\DashboardLogs;
use App\Enums\Activity\Activities;
use App\Enums\AStatusCodeResponse;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\UserTypes\UserType;
use App\Events\Users\ApprovePendingStore;
use App\Exports\Dashboard\StoresExport;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Dashboard\Stores\UpdateStoreAuthDataRequest;
use App\Http\Requests\RateStoreRequest;
use App\Http\Requests\SellerApp\CreateStoreRequest;
use App\Http\Requests\SellerApp\SyncStoreBadgesRequests;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\Seller;
use App\Models\Store;
use App\Models\User;
use App\Repositories\StoreRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use function Aws\boolean_value;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use App\Services\Notifications\NotificationsService;

class StoresController extends BaseController
{
    /**
     * @var StoreRepository
     */
    private $storeRepository;
    private $notificationService;

    public function __construct(StoreRepository $storeRepository,NotificationsService $notificationService)
    {
        $this->storeRepository = $storeRepository;
        $this->notificationService = $notificationService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $offset = $request->query('offset') ? $request->query('offset') : 0;
            $limit = $request->query('limit') ? $request->query('limit') : config('dashboard.pagination_limit');

            $query = Store::query()
                ->select('id', 'name', 'mobile', 'city_id', 'store_type_id', 'user_id', 'is_verified', 'created_at', 'stores.activation')
                ->orderBy('updated_at', 'desc');
            if ($request->filled('id')) {
                $query->where('id', $request->id);
            }
            if ($request->filled('category')) {
                $query->whereHas('categories', function ($q) use ($request) {
                    $q->where('category_id', intval($request->category));
                });
            }
            if ($request->filled('name')) {
                $searchQuery = "%" . $request->name . "%";
                $query->where('name', "ilike", $searchQuery);
            }
            if ($request->filled('id')) {
                $query->where('id', intval($request->id));
            }
            if ($request->filled('is_verified')) {
                $query->where('is_verified', $request->is_verified);
            }
            if ($request->filled('mobile')) {
                $query->where('mobile', 'Like', '%' . request('mobile') . '%');
            }
            if ($request->filled('type')) {
                $query->whereHas('type', function ($q) use ($request) {
                    $q->where('store_type_id', intval($request->type));
                });
            }
            if ($request->filled('owner')) {
                $query->whereHas('owner', function ($q) use ($request) {
                    $q->where('user_id', intval($request->owner));
                });
            }
            if ($request->filled('activation')) {
                $query->whereHas('owner', function ($q) use ($request) {
                    $q->where('activation', $request->activation);
                });
            }
            if ($request->filled('city')) {
                $query->whereHas('city', function ($q) use ($request) {
                    $q->where('id', intval($request->city));
                });
            }

            $count = $query->count();
            $stores = $query->offset($offset)->limit($limit)->get();

            foreach ($stores as $store) {
                $owner = $store->owner;
                $store->owner_id = $owner->id;
                $store->owner_email = $owner->email;
                $store->owner_mobile = $owner->mobile;
                // $store->activation = $owner->activation;
                $store->owner = $owner;
                $city = $store->city['name_en'];
                unset($store->city);
                $store->city = $city;
                $storeType = $store->type->name_en;
                $typeId = $store->type->id;
                unset($store->type);
                unset($store->store_type_id);
                $store->type = $storeType;
                $store->type_id = $typeId;
                $categories = [];
                unset($store->store_type_id);
                unset($store->owner);

                foreach ($store->categories as $key => $category) {
                    unset($category->store_id);
                    unset($category->category_id);
                    $categories[] = $category->category->name_en;
                }
                unset($store->categories);
                $store->categories = $categories;
            }
            return response()->json([
                'status' => true,
                'message' => 'stores retrieved successful',
                'data' => $stores,
                'offset' => (int)$offset,
                'limit' => (int)$limit,
                'total' => $count,
            ]);
        } catch (\Exception $exception) {
            Log::error('error in index of dashboard stores' . __LINE__ . $exception);
            return $this->connectionError($e);
        }
    }

    //    public function store(CreateStoreRequest $request)
    //    {
    //        try {
    //            $data = $request->all();
    //            $store = new Store();
    //            $seller = Seller::where('user_id', $request->seller_id)->first();
    //            $data['seller_id'] = $seller->id;
    //            $store->initializeStoreData($data);
    //            $store->save();
    //            $logo = $request->logo;
    //            $store->logo = UploadImage::uploadImageToStorage($logo, 'stores');
    //            $image = $request->image;
    //            $store->image = UploadImage::uploadImageToStorage($image, 'stores');
    //            $licence = $request->licence;
    //            $store->licence = UploadImage::uploadImageToStorage($licence, 'storesLicence');
    //            $store->save();
    //            return response()->json([
    //                "status" => true,
    //                "message" => "store created successfully",
    //                "data" => []
    //            ], AResponseStatusCode::SUCCESS);
    //        } catch (\Exception $e) {
    //            return ServerError::handle($e);
    //        }
    //    }

    //    public function show($id)
    //    {
    //        try {
    //            $store = Store::query()
    //                ->with(['categories', 'brands', 'type', 'city', 'SellerRate', 'openHours', 'storeImages'])
    //                ->where('id', $id)
    //                ->first();
    //            $categories = [];
    //            $brands = [];
    //            $openHours = [];
    //            foreach ($store->categories as $key => $category) {
    //                unset($category->store_id);
    //                unset($category->category_id);
    //                $categories[$key]['name_en'] = $category->category->name_en;
    //                $categories[$key]['name_ar'] = $category->category->name_ar;
    //            }
    //            unset($store->categories);
    //
    //            $store->categories = $categories;
    //            if (count($store->SellerRate) > 0) {
    //                $store->rate = $store->SellerRate[0]->rate;
    //            } else {
    //                $store->rate = 0;
    //            }
    //            unset($store->SellerRate);
    //
    //            foreach ($store->brands as $key => $brand) {
    //                $brands[$key]['id'] = $brand->id;
    //                $brands[$key]['name'] = $brand->name;
    //            }
    //            unset($store->brands);
    //            $store->brands = $brands;
    //            unset($store->city->id);
    //            unset($store->type->id);
    //
    //            foreach ($store->openHours as $k => $hour) {
    //                $openHours[$k]['open_time'] = $hour->open_time;
    //                $openHours[$k]['close_time'] = $hour->close_time;
    //                $openHours[$k]['day'] = $hour->day->name_en . '|' . $hour->day->name_ar;
    //            }
    //            unset($store->openHours);
    //            $store->open_hours = $openHours;
    //
    //            $images = [];
    //            foreach ($store->storeImages as $image) {
    //                $images[] = config('filesystems.aws_base_url') . $image->image;
    //            }
    //            $store->images = $images;
    //            unset($store->storeImages);
    //
    //            $store->logo = config('filesystems.aws_base_url') . $store->logo;
    //            $store->licence = config('filesystems.aws_base_url') . $store->licence;
    //
    //            return response()->json([
    //                "status" => true,
    //                "message" => "seller retrieved successfully",
    //                "data" => $store
    //            ], AStatusCodeResponse::SUCCESSFUL);
    //        } catch (\Exception $e) {
    //            return ServerError::handle($e);
    //        }
    //    }

    // TODO make it show method
    public function showStoreData($id)
    {
        try {
            $store = Store::query()
                ->leftJoin('users', 'users.id', '=', 'stores.user_id')
                ->select(
                    'stores.id',
                    'stores.name',
                    'stores.logo',
                    'stores.licence',
                    'stores.cover',
                    'stores.mobile',
                    'users.id as owner_id',
                    'users.email as owner_email',
                    'users.mobile as owner_mobile',
                    'stores.activation as activation',
                    'stores.store_type_id',
                    'stores.description',
                    'stores.created_at',
                    'stores.is_store_has_delivery',
                    'stores.is_verified_licence',
                    'stores.is_verified_cover',
                    'stores.is_verified_logo'
                )
                ->where('stores.id', $id)
                ->with('badges')
                ->first();

            if (!$store) {
                return response()->json([
                    "status" => false,
                    "message" => "store not found",
                    "data" => ''
                ], AStatusCodeResponse::BAD_REQUEST);
            }
            $store->logo = config('filesystems.aws_base_url') . $store->logo;
            $store->licence = config('filesystems.aws_base_url') . $store->licence;
            $store->cover = config('filesystems.aws_base_url') . $store->cover;

            return response()->json([
                "status" => true,
                "message" => "store data",
                "data" => $store
            ], AStatusCodeResponse::SUCCESSFUL);
        } catch (\Exception $e) {
            Log::error('error in showStoreData of dashboard stores' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function approvePending(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'store_id' => 'required|exists:users,id'
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            DB::beginTransaction();
            $store = Store::query()->where('user_id', $request->store_id)->first();


            User::query()->where('id', $request->store_id)
                ->update(['activation' => true]);
            Store::query()->where('user_id', $request->store_id)->update(['is_verified' => true]);
            $seller = User::query()->where('id', $request->store_id)->first();
            event(new ApprovePendingStore([$request->store_id]));
            $data['subject'] = "Welcome";
            $data['message'] = "Welcome to elwekala ";
            $data['user_name'] = $seller->name;
            // Mail::to($request->email)->send(new SendMail($data));
            $logData['id'] = $request->store_id;
            $logData['ref_name_en'] = $seller->name;
            $logData['ref_name_ar'] = $seller->name;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::APPROVE_SELLER;
            event(new DashboardLogs($logData, 'stores'));
            $this->notificationService->sendnotificationsToStoreActivationAccount($store);
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Store Approved Successfully',
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in approvePendingStore of dashboard seller' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }
    }

    public function showStoreCredentials($id)
    {
        try {
            $store = Store::query()
                ->where('stores.id', $id)
                ->with('owner')
                ->first();

            if (!$store) {
                return response()->json([
                    "status" => false,
                    "message" => "store not found",
                    "data" => ''
                ], AStatusCodeResponse::BAD_REQUEST);
            }
            return response()->json([
                "status" => true,
                "message" => "store data",
                "data" => $store->owner
            ], AStatusCodeResponse::SUCCESSFUL);
        } catch (\Exception $e) {
            Log::error('error in showStoreData of dashboard stores' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function showStoreMainAddress($id)
    {
        try {
            $storeAddress = Store::query()
                ->select(
                    'user_id as seller_id',
                    'address',
                    'latitude',
                    'longitude',
                    'building_no',
                    'landmark',
                    'main_street',
                    'side_street',
                    'city_id'
                )
                ->where('id', $id)
                ->first();

            if (!$storeAddress) {
                return response()->json([
                    "status" => false,
                    "message" => "store address not found",
                    "data" => ''
                ], AStatusCodeResponse::BAD_REQUEST);
            }

            return response()->json([
                "status" => true,
                "message" => "store address",
                "data" => $storeAddress
            ], AStatusCodeResponse::SUCCESSFUL);
        } catch (\Exception $e) {
            Log::error('error in showStoreMainAddress of dashboard stores' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function updateStore(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|numeric|exists:stores,id',
                'name' => 'required|string',
                // 'store_type_id' => 'required|numeric|exists:store_types,id',
                // 'mobile' => 'required|mobile_number|size:11',
                'is_store_has_delivery' => 'required|in:true,false',
                // 'is_main_branch' => 'required|in:true,false',
                'activation' => 'required|in:true,false',
                'badges_id' => 'required',
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            $store = Store::query()->find($request->id);
            $store->name = $request->name;
            // $store->store_type_id = $request->store_type_id;
            // $store->mobile = $request->mobile;
            $store->is_store_has_delivery = $request->is_store_has_delivery;
            $store->description = $request->description;
            // $store->is_main_branch = $request->is_main_branch;
            $store->activation = $request->activation;
            if ($request->hasFile('logo')) {
                Storage::disk('s3')->delete($store->logo);
                $store->logo = UploadImage::uploadImageToStorage($request->logo, 'stores');
            }
            if ($request->hasFile('licence')) {
                Storage::disk('s3')->delete($store->licence);
                $store->licence = UploadImage::uploadImageToStorage($request->licence, 'stores');
            }
            // if ($request->filled('password')) {
            //     $store->owner->password = bcrypt($request->password);
            //     $store->owner->save();
            // }
            $store->save();
            $badges = json_decode($request->badges_id);
            $store->badges()->sync($badges);
            return response()->json([
                "status" => true,
                "message" => "Store Updated successfully",
                "data" => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in updateStore of dashboard stores' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }
    }

    public function updateStoreDocumentsStatus(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|numeric|exists:stores,id',
                'is_verified_logo' => 'required|in:true,false,null',
                'is_verified_cover' => 'required|in:true,false,null',
                'is_verified_licence' => 'required|in:true,false,null',
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            $store = Store::query()->find($request->id);
            $store->is_verified_logo = ($request->is_verified_logo == 'null' ? null : $request->is_verified_logo);
            $store->is_verified_cover = ($request->is_verified_cover == 'null' ? null : $request->is_verified_cover);
            $store->is_verified_licence = ($request->is_verified_licence == 'null' ? null : $request->is_verified_licence);
            $store->save();
            return response()->json([
                "status" => true,
                "message" => "Store Updated successfully",
                "data" => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in update Store documents of dashboard stores' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }
    }

    public function updateStoreAuth(UpdateStoreAuthDataRequest $request)
    {
        try {
            $this->storeRepository->updateAuthData($request->validated());

            return response()->json([
                "status" => true,
                "message" => "Store Updated successfully",
                "data" => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in updateStore of dashboard stores' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function updateStoreAddress(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'seller_id' => 'required|numeric|exists:users,id',
                'city_id' => 'required|numeric|exists:cities,id',
                'address' => 'required|string|max:255',

            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            $storeAddress = Store::query()
                ->where('user_id', $request->seller_id)
                ->first();
            $storeAddress->address = $request->address;
            $storeAddress->city_id = $request->city_id;
            $storeAddress->latitude = $request->latitude;
            $storeAddress->longitude = $request->longitude;
            $storeAddress->building_no = $request->building_no;
            $storeAddress->landmark = $request->landmark;
            $storeAddress->main_street = $request->main_street;
            $storeAddress->side_street = $request->side_street;
            $storeAddress->save();

            return response()->json([
                "status" => true,
                "message" => "Store Updated successfully",
                "data" => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in updateStoreAddress of dashboard stores' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }


    public function delete(Request $request)
    {
        try {
            $store = Store::find($request->id);
            $store->delete();
            return response()->json([
                "status" => AResponseStatusCode::SUCCESS,
                "message" => "deleted successfully"
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in delete of dashboard stores' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getStoresForSelection(Request $request)
    {
        try {
            $query = Store::query()
                ->leftJoin('users', 'users.id', '=', 'stores.user_id');
            if ($request->filled('category')) {
                $query->leftJoin('category_store', 'category_store.store_id', '=', 'stores.id');
                $query->whereIn('category_store.category_id', explode(',', $request->query('category')));
            }
            $query->where('users.activation', true)
                ->select(['stores.id as id', 'stores.name as name']);
            if ($request->filled('name')) {
                $query->where('stores.name', "like", "%" . $request->query('name') . "%");
            }
            $stores = $query->get();
            return response()->json([
                'message' => '',
                'data' => $stores
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getStoresForSelection of dashboard stores' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getStoreDetails(Request $request)
    {
        try {
            $data = $request->all();
            $query = Store::query()
                ->leftJoin('users', 'users.id', '=', 'stores.user_id')
                ->select(['stores.id as id', 'stores.name as name']);

            if (isset($data['user_id']) && $data['user_id'] != null) {
                $query->where('users.id', $data['user_id']);
            }
            if (isset($data['store_id']) && $data['store_id'] != null) {
                $query->where('stores.id', $data['store_id']);
            }

            $store = $query->first();
            info($store);
            return response()->json([
                'message' => '',
                'data' => $store
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in store details' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getStoreByUserId(Request $request, $id)
    {
        try {

            $store = Store::query()
                ->leftJoin('users', 'users.id', '=', 'stores.user_id')
                ->select(['stores.id as id', 'stores.name as name'])
                ->where('users.id', $id)->first();
            return response()->json([
                'message' => '',
                'data' => $store
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in store details' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getStoreById(Request $request, $id)
    {
        try {

            $store = Store::query()
                ->select(['id', 'name'])
                ->where('id', $id)->first();
            return response()->json([
                'message' => '',
                'data' => $store
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in store details' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }


    public function syncBadges(SyncStoreBadgesRequests $request)
    {
        try {
            $this->storeRepository->syncBadges($request->validated());
            return response()->json([
                'message' => 'badges',
                'data' => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in Export Products in dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function export(Request $request)
    {
        try {
            return Excel::download(new StoresExport($request), 'stores.xlsx');
        } catch (\Exception $e) {
            Log::error('error in Stores in dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function changeStoreStatus($id, $status)
    {
        try {
            $store = Store::query()->where('id', $id)->first();
            $store->is_verified = boolean_value($status);
            $store->save();
            return response()->json([
                "status" => true,
                "message" => "Store status Updated successfully",
                "data" => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in updateStore of dashboard stores' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function viewDocuments($store_id)
    {
        $store = Store::query()->where('id', $store_id)->first();
        if ($store) {
            $documents['logo'] = config('filesystems.aws_base_url') . $store->logo;
            $documents['licence'] = config('filesystems.aws_base_url') . $store->licence;
            $documents['cover'] = config('filesystems.aws_base_url') . $store->licence;
            return response()->json([
                "status" => true,
                "message" => "Store Documents",
                "data" => $documents
            ], AResponseStatusCode::SUCCESS);
        }
        return response()->json([
            "status" => false,
            "message" => "Store Not Found",
            "data" => []
        ], AResponseStatusCode::NO_CONTENT);
    }

    public function verifyDocument($store_id, $document_type, $status)
    {
        try {
            $store = Store::query()->where('id', $store_id)->first();
            if ($store) {
                if ($status == 0) {
                    $status = false;
                } elseif ($status == 1) {
                    $status = true;
                }
                if ($document_type == 1) {
                    $store->is_verified_logo = $status;
                } elseif ($document_type == 2) {
                    $store->is_verified_cover = $status;
                } elseif ($document_type == 3) {
                    $store->is_verified_licence = $status;
                }
                $store->save();
                return response()->json([
                    "status" => true,
                    "message" => "Store Change Documents Status",
                    "data" => ''
                ], AResponseStatusCode::SUCCESS);
            }
            return response()->json([
                "status" => false,
                "message" => "Store Not Found",
                "data" => []
            ], AResponseStatusCode::NO_CONTENT);
        } catch (\Exception $e) {
            Log::error('error in update Store documents of dashboard stores' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function rateStoreByAdmin(RateStoreRequest $request)
    {
        try {
            $this->storeRepository->rateStoreByAdmin($request);
            return response()->json([
                "status" => true,
                "message" => "Store Rated Successfully",
                "data" => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in rateStoreByAdmin of dashboard stores' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
