<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Lib\Log\ServerError;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class RolesController extends BaseController
{
    public function getForSelection()
    {
        try {
            $roles = Role::select('id', 'role as name')
                ->orderBy('updated_at', 'desc')
                ->get();
            return response()->json([
                'success' => true,
                'message' => 'roles retrieved successfully',
                'roles' => $roles
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getForSelection of dashboard roles' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
