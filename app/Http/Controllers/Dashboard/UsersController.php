<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Lib\Log\ServerError;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class UsersController extends BaseController
{
    public function getForSelection(Request $request)
    {
        try {

            $query = User::query()
                ->select('id', 'name')
                ->where('type_id', $request->type_id);
            if ($request->filled("name")) {
                $query->where('name','like','%'.$request->name.'%');
            }
            $users =  $query->get();
            return response()->json([
                "status" => AResponseStatusCode::SUCCESS,
                "message" => "offer types retrieved successfully",
                "data" => $users
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getForSelection of dashboard users' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
