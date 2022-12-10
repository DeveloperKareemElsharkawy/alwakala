<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\Activity\Activities;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Events\Logs\DashboardLogs;
use App\Http\Controllers\BaseController;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Models\PermissionRole;
use App\Models\Admin;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PermissionController extends BaseController
{

    public function getPermissions(Request $request)
    {
        try {
            $permissions = Permission::query()
                ->orderBy('updated_at', 'desc')
                ->get();
            return response()->json([
                'success' => true,
                'message' => 'Permissions retrieved successfully',
                'data' => $permissions
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getPermissions of dashboard Permissions' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getPermissionForAdmin(Request $request)
    {
        try {
            $roleId = Admin::query()->with(['User' => function ($query) use ($request) {
                $query->where('id', $request->user_id);
            }])->select('role_id')->first();

            $permissionRoles = PermissionRole::query()
                ->where('role_id', $roleId->role_id)->select(['permission_id'])->get();

            $permissions = Permission::query()->whereIn('id', $permissionRoles)->get();

            $result = [];
            foreach ($permissions as $permission) {
                $result[] = $permission->permission_name;
            }

            $data['admin_permissions'] = $result;

            return response()->json([
                'success' => true,
                'message' => 'Admin Permission retrieved successfully',
                'data' => $data
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in getPermissionForAdmin of dashboard Permissions' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

    public function getRolesPermissions(Request $request)
    {
        try {

            $rolesPermissions = PermissionRole::query()->get();

            return response()->json([
                'success' => true,
                'message' => 'Roles Permissions retrieved successfully',
                'data' => $rolesPermissions
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in getRolesPermissions of dashboard Permissions' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function togglePermissions(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'role_id' => "required|numeric|exists:roles,id",
                'permission_id' => "required|numeric|exists:permissions,id"
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            $existance = PermissionRole::query()->where('role_id', $request->role_id)
                ->where('permission_id', $request->permission_id)->first();

            if ($existance) {
                $permissionRole=  PermissionRole::query()->where('role_id', $request->role_id)
                    ->where('permission_id', $request->permission_id)->delete();
                $logData['id'] = $existance->id;
                $logData['user'] = $request->user;
                $logData['action'] = Activities::DELETE_PERMISSION;
                event(new DashboardLogs($logData, 'permissions'));
            } else {
              $permissionRole=  PermissionRole::query()->create([
                    'role_id' => $request->role_id,
                    'permission_id' => $request->permission_id
                ]);
                $logData['id'] = $permissionRole->id;
                $logData['user'] = $request->user;
                $logData['action'] = Activities::CREATE_PERMISSION;
                event(new DashboardLogs($logData, 'permissions'));
            }

            return response()->json([
                'success' => true,
                'message' => 'success',
                'data' => []
            ], AResponseStatusCode::SUCCESS);


        } catch (\Exception $e) {
            Log::error('error in togglePermissions of dashboard Permissions' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

}
