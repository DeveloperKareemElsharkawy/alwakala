<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\UserTypes\UserType;
use App\Http\Controllers\BaseController;
use App\Lib\Log\ServerError;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class PendingUsersController extends BaseController
{
    public function index(Request $request)
    {
        try {
            $query = User::query()
                ->where('activation', false)
                ->where('type_id', UserType::SELLER)
                ->orderBy('updated_at', 'desc');
            if ($request->filled('query')) {
                $searchQuery = "%" . $request->get("query") . "%";
                $query->where('users.name', "ilike", $searchQuery)
                    ->orWhere('users.mobile', "ilike", $searchQuery);
            }
            $pendingSellers = $query->paginate(20);

            foreach ($pendingSellers as $seller) {
                if ($seller->image)
                    $seller->image = config('filesystems.aws_base_url') . $seller->image;
            }
            return response()->json([
                'success' => true,
                'message' => 'Sellers',
                'data' => $pendingSellers
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in index of dashboard PendingUsers' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
