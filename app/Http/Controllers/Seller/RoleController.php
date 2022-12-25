<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\Roles\ARoles;
use App\Enums\UserTypes\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shared\RegisterUserRequest;
use App\Http\Requests\Users\CreateUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Resources\RolesResource;
use App\Http\Resources\Seller\ModeratorResource;
use App\Lib\Log\ServerError;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Seller;
use App\Models\Store;
use App\Models\User;
use App\Repositories\StoreRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleController extends BrandsController
{
    public function index(Request $request)
    {
        try {
            $store = Store::query()
                ->select('id')
                ->where('user_id', $request->user_id)->first();

            $users = User::query()
                ->select('users.id', 'users.name', 'users.image', 'users.email', 'users.mobile', 'roles.id as role_id', 'roles.role', 'users.activation')
                ->join('sellers', 'sellers.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'sellers.role_id')
                ->where('sellers.store_id', $store->id)
                ->where('roles.role', '!=', ARoles::OWNER)
                ->get();

            $response = ModeratorResource::collection($users);
            return response()->json([
                'status' => true,
                'message' => trans('messages.general.listed'),
                'data' => $response
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in create new user' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function createUser(CreateUserRequest $request)
    {
        try {
            $ownerRole = Role::query()->where('role', ARoles::OWNER)->first();
            if ($request->role_id == $ownerRole->id) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.roles.cannot_creat_owner'),
                    'data' => []
                ], AResponseStatusCode::BAD_REQUEST);
            }
            $data = $request->all();
            $store = StoreRepository::getStoreByUserId($request->user_id);
            DB::beginTransaction();
            $user = new User();
            $data['type_id'] = UserType::SELLER;
            $user->initializeUserFields($data);
            $user->save();
            User::query()->where('id', $user->id)
                ->update(['activation' => true]);

            $seller = new Seller();
            $seller->user_id = $user->id;
            $seller->store_id = $store->id;
            $seller->role_id = $request->role_id;
            $seller->save();

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => trans('messages.auth.user_created'),
                'data' => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in create new user' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function editUser(UpdateUserRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = UserRepository::getUserById($request->id);
            $user->name = $request->name;
            $user->mobile = $request->mobile;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            if ($request->image) {
                $user->image = UploadImage::uploadImageToStorage($request->image, 'sellers/');
            }
            $user->save();

            $seller = Seller::query()->where('user_id', $request->id)->first();
            $seller->role_id = $request->role_id;
            $seller->save();
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => trans('messages.auth.user_updated'),
                'data' => []
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            DB::rollBack();
            return ServerError::handle($e);
            Log::error('error in editing user' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function deactivateUser(Request $request)
    {
        try {
            UserRepository::deleteUser($request->id);
            return response()->json([
                'status' => true,
                'message' => trans('messages.auth.user_deleted'),
                'data' => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in addOrder of seller Order' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getRolesForUserType($user_type)
    {
        try {
            $roles = UserRepository::getRolesByUserType($user_type);
            return response()->json([
                'status' => true,
                'message' => '',
                'data' => ['roles' => $roles]
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in list roles' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getRolesForSelection()
    {
        try {
            $roles = Role::query()
                ->select('id', 'role')
                ->where('type', UserType::SELLER)
                ->where('role', '!=', ARoles::OWNER)
                ->get();
            $response = RolesResource::collection($roles);
            return response()->json([
                'status' => true,
                'message' => '',
                'data' => $response
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in list roles' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getUserRolesPermissions(Request $request)
    {
        try {
            $user = User::query()->with('roles', 'roles.Permissions')->find($request->seller_id);

            $permissions = $user->roles->pluck('Permissions.*.permission_name')->flatten()->unique()->toArray();

            return response()->json([
                'status' => true,
                'message' => '',
                'data' => $permissions
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in Logged User Permissions' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }


    public function getSellerSystemPermissions(Request $request)
    {
        try {
            $permissions = Permission::query()
                ->where('permission_name', 'like', "s%")
                ->get()->pluck('permission_name')->toArray();

            return response()->json([
                'status' => true,
                'message' => '',
                'data' => $permissions
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in Logged User Permissions' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }


}
