<?php


namespace App\Repositories;


use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Role;
use App\Models\Seller;
use App\Models\User;
use App\Enums\Stock\ATransactionTypes;
use App\Events\Inventory\StockMovement;
use App\Lib\Log\ServerError;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\BarcodeProduct;
use App\Models\Bundle;
use App\Models\Color;
use App\Models\PackingUnitProduct;
use App\Models\PackingUnitProductAttribute;
use App\Models\ProductConsumerPrice;
use App\Models\ProductImage;
use App\Models\Product;
use App\Models\ProductStore;
use App\Models\Store;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserRepository extends Controller
{

    public static function getPermissionsForAdmin($userId)
    {
        try {
            $roleId = Admin::query()->where('user_id', $userId)->first()->role_id;
            return PermissionRole::query()->where('role_id', $roleId)->get()->pluck('permission_id')->toArray();
        } catch (\Exception $e) {
            info('error fetching permissions' . $e);
            return false;
        }
    }

    public static function getPermissionsForSeller($userId)
    {
        try {
            $roleId = Seller::query()->where('user_id', $userId)->first()->role_id;
            return PermissionRole::query()->where('role_id', $roleId)->get()->pluck('permission_id')->toArray();
        } catch (\Exception $e) {
            info('error fetching permissions' . $e);
            return [];
        }
    }

    public static function getPermissionsForUserByName($permission_name)
    {
        $permission = Permission::query()->where('permission_name', $permission_name)->first();
        if ($permission) {
            return $permission->id;
        }
        return null;
    }

    public static function newSeller($data)
    {
        $seller = Seller::firstOrNew(['user_id' => $data['user_id']]);

        $seller->user_id = $data['user_id'];
        if (isset($data['store_id'])) {
            $seller->store_id = $data['store_id'];
        }
        $seller->role_id = $data['role_id'];
        $seller->save();
    }

    public static function getUserRoleByName($name, $type)
    {
        return Role::query()->where(['role' => $name, 'type' => $type])->first()->id;
    }

    public static function getUserById($id)
    {
        return User::query()->where('id', $id)->first();
    }

    public static function deleteUser($id)
    {
        try {
            DB::beginTransaction();
            Seller::query()->where('user_id', $id)->delete();
            User::query()->where('id', $id)->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }

    }

    public static function getRolesByUserType($userType)
    {
        return Role::query()->select(['id', 'role'])->where('type', $userType)->get();
    }

    public static function getUsersIdsToStore($request)
    {
        $store = StoreRepository::getStoreByUserId($request->user_id);
        return Seller::query()->where('store_id', $store->id)->pluck('user_id')->toArray();
    }
}
