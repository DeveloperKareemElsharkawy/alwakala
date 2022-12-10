<?php

namespace App\Http\Middleware;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Lib\Helpers\UserId\UserId;
use App\Repositories\UserRepository;
use Closure;
use Illuminate\Http\Request;

class CheckIfAdminCanAccess
{
    /**
     * Handle an incoming request.
     * @param Request $request
     * @param Closure $next
     * @param null $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission = null)
    {
        $userId = UserId::UserId($request);
        $userPermissionsIds = UserRepository::getPermissionsForAdmin($userId);
        $permissionId = UserRepository::getPermissionsForUserByName($permission);
        $can = false;
        if (in_array($permissionId, $userPermissionsIds)) {
            $can = true;
        }
        if (!$can) {
            return response()->json([
                'success' => false,
                'message' => trans('messages.auth.access_denied'),
                'data' => ''
            ], AResponseStatusCode::FORBIDDEN);
        }
        return $next($request);
    }
}
