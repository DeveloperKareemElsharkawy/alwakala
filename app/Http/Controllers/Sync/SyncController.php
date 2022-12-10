<?php

namespace App\Http\Controllers\Sync;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    public function syncUsers(Request $request)
    {
        $users = User::query()
            ->whereIn('id', $request->input("user_ids"))
            ->select('id', 'name', 'image')
            ->get();

        foreach ($users as $user) {
            $user->image = $user->image ? config('filesystems.aws_base_url') . $user->image : NULL;
        }

        return response()->json([
            'status' => true,
            'data' => $users
        ], AResponseStatusCode::SUCCESS);
    }


    public function syncStores(Request $request)
    {
        $stores = Store::query()
            ->whereIn('id', $request->input("user_ids"))
            ->select('id', 'name', 'logo')
            ->get();


        foreach ($stores as $store) {
            $store->logo = $store->logo ? config('filesystems.aws_base_url') . $store->logo : NULL;
        }
        return response()->json([
            'status' => true,
            'data' => $stores
        ], AResponseStatusCode::SUCCESS);
    }
}
