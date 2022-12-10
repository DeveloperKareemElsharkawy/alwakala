<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\Activity\Activities;
use App\Enums\AStatusCodeResponse;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\UserTypes\UserType;
use App\Events\Logs\DashboardLogs;
use App\Exports\Dashboard\AdminsExport;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\RegisterAdmin;
use App\Http\Requests\Admins\UpdateAdmin;
use App\Http\Requests\Shared\LoginRequest;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use \Maatwebsite\Excel\Excel as Excel2;
class AdminsController extends BaseController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = User::query()
                ->with('admin')
                ->select('id', 'name', 'email', 'mobile', 'activation')
                ->orderByRaw('users.updated_at DESC NULLS LAST');
            $offset = $request->query('offset') ? $request->query('offset') : 0;
            $limit = $request->query('limit') ? $request->query('limit') : config('dashboard.pagination_limit');
            if ($request->filled("id")) {
                $query->where('id', intval($request->id));
            }
            if ($request->filled("name")) {
                $name = "%" . $request->get("name") . "%";
                $query->where('name', "ilike", $name);
            }
            if ($request->filled("email")) {
                $email = "%" . $request->get("email") . "%";
                $query->where('email', "ilike", $email);
            }
            if ($request->filled("mobile")) {
                $mobile = "%" . $request->get("mobile") . "%";
                $query->where('mobile', "ilike", $mobile);
            }
            if ($request->filled("activation")) {
                $query->where('activation', $request->activation);
            }
            if ($request->filled("sort_by_id")) {
                $query->orderBy('id', intval($request->sort_by_id));
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
            $query->where('type_id', UserType::ADMIN);
            $count = $query->count();
            $admins = $query->offset($offset)->limit($limit)->get();

            foreach ($admins as $admin) {
                $role = $admin->admin->role->role ?? null;
                unset($admin->admin);
                $admin->role = $role;
                if ($admin->image)
                    $admin->image = config('filesystems.aws_base_url') . $admin->image;
            }
            return response()->json([
                "status" => true,
                "message" => "admins retrieved successfully",
                "data" => $admins,
                'offset' => (int)$offset,
                'limit' => (int)$limit,
                'total' => $count,
            ], AStatusCodeResponse::SUCCESSFUL);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in index of dashboard admins ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param RegisterAdmin $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(RegisterAdmin $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $user = new User();
            $data['type_id'] = UserType::ADMIN;
            $user->initializeUserFields($data);
            $user->save();
            $admin = new Admin();
            $admin->role_id = 1; // TODO add roles of users
            $admin->user_id = $user->id;
            $admin->save();
            $logData['id'] = $user->id;
            $logData['ref_name_ar'] = $user->name;
            $logData['ref_name_en'] = $user->name;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::CREATE_ADMIN;
            event(new DashboardLogs($logData, 'admins'));
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Admin Created',
                'data' => []
            ], AStatusCodeResponse::SUCCESSFUL);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in store of dashboard admins ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $admin = User::query()
                ->with('admin')
                ->where('type_id', UserType::ADMIN)
                ->where('id', $id)
                ->first();
            if (!is_null($admin)) {
                $admin->roleId = $admin->admin->role_id;
                if ($admin->image)
                    $admin->image = config('filesystems.aws_base_url') . $admin->image;
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'There is an error',
                    'data' => []
                ], AResponseStatusCode::NOT_FOUNT);
            }
            $role = $admin->admin->role->role;
            unset($admin->admin);
            $admin->role = $role;
            return response()->json([
                "status" => true,
                "message" => "admins retrieved successfully",
                "data" => $admin
            ], AStatusCodeResponse::SUCCESSFUL);

        } catch (\Exception $e) {
            Log::error('error in show of dashboard admins ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param UpdateAdmin $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateAdmin $request)
    {
        try {
            $admin = User::query()->find($request->id);
            $admin->name = $request->name;
            if ($request->filled('password')) {
                $admin->password = bcrypt($request->password);
            }
            $admin->email = $request->email;
            $admin->mobile = $request->mobile;
            $admin->activation = $request->activation;
            if ($request->hasFile('image')) {
                Storage::disk('s3')->delete($admin->image);
                $admin->image = UploadImage::uploadImageToStorage($request->image, 'admins');
            }
            $admin->save();
            $logData['id'] = $admin->id;
            $logData['ref_name_ar'] = $admin->name;
            $logData['ref_name_en'] = $admin->name;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::UPDATE_ADMIN;
            event(new DashboardLogs($logData, 'admins'));
            return response()->json([
                'status' => true,
                'message' => 'admin Updated',
                'data' => $admin
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in update of dashboard admins ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        try {
            $admin_user = User::query()
                ->where('id', $request->id)
                ->where('type_id', UserType::ADMIN)
                ->first();
            if (!$admin_user) {
                return response()->json([
                    "status" => false,
                    "message" => "error not valid data",
                    "data" => ""
                ], AStatusCodeResponse::SUCCESSFUL);
            }
            Storage::disk('s3')->delete($admin_user->image);
            $logData['id'] = $admin_user->id;
            $logData['ref_name_ar'] = $admin_user->name;
            $logData['ref_name_en'] = $admin_user->name;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::DELETE_ADMIN;
            event(new DashboardLogs($logData, 'admins'));
            $admin_user->delete();
            return response()->json([
                "status" => true,
                "message" => "admin deleted successfully",
                "data" => ""
            ], AStatusCodeResponse::SUCCESSFUL);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in destroy of dashboard admins ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function deleteImage($id)
    {
        try {
            $admin = User::query()->where('id', $id)->first();
            if (!$admin) {
                return response()->json([
                    'status' => false,
                    'message' => 'not found',
                    'data' => ''
                ], AStatusCodeResponse::BAD_REQUEST);
            }
            Storage::disk('s3')->delete($admin->image);
            $admin->image = null;
            $admin->save();
            return response()->json([
                'status' => true,
                'message' => 'image deleted',
                'data' => ''
            ], AStatusCodeResponse::SUCCESSFUL);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in deleteImage of dashboard admins ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function checkAuth(Request $request)
    {
        return response()->json([
            'status' => true,
            'message' => '',
            'data' => []
        ]);
    }

    public function logout()
    {
        if (Auth::check()) {
            Auth::user()->AauthAcessToken()->delete();
            return response()->json([
                "status" => AResponseStatusCode::SUCCESS,
                "message" => "لقد تم تسجيل خروجك بنجاح ",
                "data" => []
            ], AResponseStatusCode::SUCCESS);
        }
    }

    public function login2(LoginRequest $request)
    {

        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            if ($user->type_id != UserType::ADMIN || $user->activation != true) {
                return response()->json(
                    [
                        'status' => AResponseStatusCode::UNAUTHORIZED,
                        'message' => 'check your credentials'
                    ], AResponseStatusCode::UNAUTHORIZED);
            }

            $admin = Admin::query()
                ->with('User')
                ->where('user_id', $user->id)
                ->first();
            if (!$admin) {
                return response()->json([
                    'status' => AResponseStatusCode::UNAUTHORIZED,
                    'message' => 'check your credentials'], AResponseStatusCode::UNAUTHORIZED);
            }
            $token = $user->createToken('myApp')->accessToken;
            $admin->token = $token;
            $admin->name = $admin->user->name;
            $admin->email = $admin->user->email;
            $admin->mobile = $admin->user->mobile;
            if ($admin->mobile) {
                $admin->image = config('filesystems.aws_base_url') . $admin->user->image;
            }
            return response()->json([
                'message' => '',
                'status' => AResponseStatusCode::SUCCESS,
                'data' => $admin,
            ], AResponseStatusCode::SUCCESS);
        } else {
            return response()->json([
                'status' => AResponseStatusCode::UNAUTHORIZED,
                'message' => 'check your credentials'], AResponseStatusCode::UNAUTHORIZED);
        }
    }

    public function getAdmins(Request $request)
    {
        try {
            $admins = User::query()
                ->where('type_id', '=', UserType::ADMIN)
                ->select(['id', 'name', 'email', 'mobile', 'activation'])
                ->get();

            return response()->json([
                'status' => true,
                'message' => '',
                'data' => $admins
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in getAdmins of dashboard admins ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function export(Request $request)
    {
        try {
//            return Excel::download(new AdminsExport($request), 'admins.xlsx');
            return (new AdminsExport($request))->download('admins.xlsx', Excel2::CSV, [
                'Content-Type' => 'text/csv',
            ]);
        } catch (\Exception $e) {
            Log::error('error in colors in dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }


}
