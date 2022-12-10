<?php


namespace App\Repositories;


use App\Lib\Helpers\UserId\UserId;
use App\Models\Package;
use App\Models\PackageSubscribe;
use App\Models\Store;
use Illuminate\Support\Facades\DB;

class PackagesRepository
{

    public function addPackage($request)
    {
        $new = new Package();
        $new->name_ar = $request->name_ar;
        $new->name_en = $request->name_en;
        $new->period = $request->period;
        $new->product_limitation = $request->product_limitation;
        $new->price = $request->price;
        $new->store_type_id = $request->type;
        $new->description = $request->description;
        $new->save();
    }

    public function updatePackage($request)
    {
        $new = Package::query()->where('id', $request->id)->first();
        $new->name_ar = $request->name_ar;
        $new->name_en = $request->name_en;
        $new->period = $request->period;
        $new->product_limitation = $request->product_limitation;
        $new->price = $request->price;
        $new->store_type_id = $request->type;
        $new->description = $request->description;
        $new->save();
    }

    public function deletePackage($id): bool
    {
        $package_subscribe = PackageSubscribe::query()->where(['package_id' => $id, 'status' => 2])->first();
        if ($package_subscribe) {
            return false;
        }
        Package::query()->where('id', $id)->delete();
        return true;
    }

    public function subscribeToPackage($request, $admin = false)
    {
        $package_subscribe = PackageSubscribe::query()->where(['store_id' => $request->store_id])->whereIn('status', [1, 2])->first();
        if ($package_subscribe) {
            return false;
        }

        $new = new PackageSubscribe();
        $new->package_id = $request->package_id;
        if ($admin) {
            $new->status = 2;
            $storeId = $request->store_id;
        } else {
            $userId = UserId::UserId($request);
            $store = StoreRepository::getStoreByUserId($userId);
            $storeId = $store->id;
        }
        $new->store_id = $storeId;
        $new->save();
        return true;

    }

    public function changePackageStatus($request)
    {
        $package = Package::query()->where('id', $request->package_id)->first();
        $package->active = $request->status;
        $package->save();
    }

    public static function deactivateStoreAfterPackageEnded()
    {
        DB::beginTransaction();
        $subscribers = PackageSubscribe::query()->select('packages.period', 'package_subscribes.store_id', 'package_subscribes.activation_date')->join('packages', 'packages.id', '=', 'package_subscribes.package_id')->where('status', '=', 2)->get();
        foreach ($subscribers as $subscriber) {
            $date1 = $subscriber->activation_date;
            $date2 = date('Y-m-d');

            $diff = abs(strtotime($date2) - strtotime($date1));

            $years = floor($diff / (365 * 60 * 60 * 24));
            $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
            $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
            // dd(intval($days),$subscriber->period);
            if ($days > $subscriber->period) {
                $store = Store::query()->where('id', $subscriber->store_id)->first();
                $store->is_verified = false;
                $store->save();
                $subscriber->status = 3;
            }
        }
        DB::commit();
    }

    public function changePackageStatusToStore($request)
    {
        $subscribe = PackageSubscribe::query()->where(['store_id' => $request->store_id, 'status' => 2])->first();
        if ($subscribe) {
            return false;
        }
        $row = PackageSubscribe::query()->where(['package_id' => $request->package_id, 'store_id' => $request->store_id])->first();
        if (!$row) {
            return false;
        }
        $row->status = $request->status;
        $row->activation_date = date('Y-m-d');
        $row->save();
        return true;


    }

    public function getPackagesByStoreTypeId($store_type_id)
    {
        return Package::query()->where(['store_type_id' => $store_type_id, 'active' => true])->paginate(10);
    }

    public function getSubscribersStores($store_type_id)
    {
        $stores = Store::query()
            ->select('stores.name', 'stores.id as store_id', 'stores.logo', 'packages.id as package_id', 'packages.price', 'packages.period')
            ->join('package_subscribes', 'package_subscribes.store_id', '=', 'stores.id')
            ->join('packages', 'package_subscribes.package_id', '=', 'packages.id')
            ->where('stores.store_type_id', $store_type_id)
            ->paginate(10);
        foreach ($stores as $store) {
            if ($store->logo)
                $store->logo = config('filesystems.aws_base_url') . $store->logo;

        }
        return $stores;

    }

    public function getPackageSubscribeByStore($request, $admin = false)
    {
        if ($admin) {
            $storeId = $request->store_id;
        } else {
            $userId = UserId::UserId($request);
            $store = StoreRepository::getStoreByUserId($userId);
            $storeId = $store->id;
        }
        $package = PackageSubscribe::query()->where(['store_id' => $storeId])->whereIn('status', [1, 2])->first();
        return Package::query()->where('id', $package->package_id)->first();

    }

}
