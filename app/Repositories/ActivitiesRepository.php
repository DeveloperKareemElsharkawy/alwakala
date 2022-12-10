<?php


namespace App\Repositories;


use App\Http\Controllers\Controller;
use App\Lib\Log\ServerError;
use App\Models\Activity;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class ActivitiesRepository extends Controller
{
    public static function log($data)
    {
        try {
            $newLog = new Activity;
            $newLog->ref_id = $data['ref_id'];
            $newLog->action = $data['action'];
            $newLog->type = $data['type'];
            $newLog->user_id = $data['user_id'];
            $newLog->save();
            return true;
        } catch (\Exception $e) {
            return ServerError::handle($e);
        }
    }

    public static function getLogs($userIds)
    {
        $logs = DB::select('
        select log_actions.ref_id,
               log_actions.created_at,
               log_actions.id as action_id ,
               log_actions.action,
               log_actions.user_id,
               users.name as user_name,
               products.name as product_name,
               products.id as product_id,
               stores.id as store_id,
               stores.name as store_name
               from actions
        left join products on log_actions.ref_id=products.id and log_actions.type=1
        left join stores on log_actions.ref_id=stores.id and log_actions.type=2
        left join users on log_actions.user_id=users.id
        order by log_actions.created_at desc
        ');
        foreach ($logs as $log) {
            $action = $log->action;
            $data[$log->action_id]['user'] = $log->user_name;
            $data[$log->action_id]['ref_id'] = $log->ref_id;
            $data[$log->action_id]['created_at'] = $log->created_at;
            if ($log->product_name) {
                $data[$log->action_id]['ref_name'] = $log->product_name;
            }
            if ($log->store_name) {
                $data[$log->action_id]['ref_name'] = $log->store_name;
            }
            $data[$log->action_id]['action'] = trans("messages.actions.$action");
        }
        return $data;
    }

    static function getRoleName($userId)
    {
        $role = Admin::query()
            ->select('role_id')
            ->where('user_id', $userId)->first();
        if ($role) {
            return Role::query()->where('id', $role->role_id)->first()->role;
        } else {
            return false;
        }
    }

    static function getDashboardLogs()
    {
        $stores = DB::connection('mongodb')->collection('stores')->get()->toArray();
        $products = DB::connection('mongodb')->collection('products')->get()->toArray();
        $sizes = DB::connection('mongodb')->collection('sizes')->get()->toArray();
        $areas = DB::connection('mongodb')->collection('areas')->get()->toArray();
        $cities = DB::connection('mongodb')->collection('cities')->get()->toArray();
        $countries = DB::connection('mongodb')->collection('countries')->get()->toArray();
        $regions = DB::connection('mongodb')->collection('regions')->get()->toArray();
        $states = DB::connection('mongodb')->collection('states')->get()->toArray();
        $zones = DB::connection('mongodb')->collection('zones')->get()->toArray();
        $admins = DB::connection('mongodb')->collection('admins')->get()->toArray();
        $brands = DB::connection('mongodb')->collection('brands')->get()->toArray();
        $categories = DB::connection('mongodb')->collection('categories')->get()->toArray();
        $colors = DB::connection('mongodb')->collection('colors')->get()->toArray();
        $consumers = DB::connection('mongodb')->collection('consumers')->get()->toArray();
        $sections = DB::connection('mongodb')->collection('sections')->get()->toArray();
        $materials = DB::connection('mongodb')->collection('materials')->get()->toArray();
        $offers = DB::connection('mongodb')->collection('offers')->get()->toArray();
        $units = DB::connection('mongodb')->collection('units')->get()->toArray();
        $permissions = DB::connection('mongodb')->collection('permissions')->get()->toArray();
        $sellers = DB::connection('mongodb')->collection('sellers')->get()->toArray();
        $suppliers = DB::connection('mongodb')->collection('suppliers')->get()->toArray();
        $systems = DB::connection('mongodb')->collection('systems')->get()->toArray();
        $app_tvs = DB::connection('mongodb')->collection('app_tvs')->get()->toArray();
        $data = collect([
            $stores,
            $products,
            $sizes,
            $areas,
            $cities,
            $countries,
            $regions,
            $states,
            $zones,
            $admins,
            $brands,
            $categories,
            $colors,
            $consumers,
            $sections,
            $materials,
            $offers,
            $units,
            $permissions,
            $sellers,
            $suppliers,
            $systems,
            $app_tvs
        ]);
        foreach ($data as $key => $item) {
            if (!$item) {
                unset($data[$key]);
            }
        }
        return $data->toArray();
    }

    static function getDashboardLogsByRefId($collection, $refId)
    {
        return DB::connection('mongodb')->collection($collection)->where('id', intval($refId))->get()->toArray();
    }

    static function getDashboardLogsByCollection($collection)
    {
        return DB::connection('mongodb')->collection($collection)->get()->toArray();
    }
}
