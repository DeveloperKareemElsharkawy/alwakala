<?php


namespace App\Repositories;


use App\Models\Commission;
use App\Models\StoresCommission;

class CommissionRepository
{
    public function createCommission($request)
    {
        $row = new Commission();
        $row->name = $request->name;
        $row->store_type_id = $request->type;
        $row->description = $request->description;
        $row->save();
    }

    public function updateCommission($request)
    {
        $row = Commission::query()->where('id', $request->id)->first();
        $row->name = $request->name;
        $row->store_type_id = $request->type;
        $row->description = $request->description;
        $row->save();
    }

    public function deleteCommission($id)
    {
        Commission::query()->where('id', $id)->delete();
    }

    public function changeCommissionStatus($request)
    {
        if ($request->status == 1) {
            $row = Commission::query()->where(['active' => 1, 'store_type_id' => $request->type])->first();
            if ($row) {
                return false;
            }

        }
        $row = Commission::query()->where('id', $request->id)->first();
        $row->active = $request->status;
        $row->save();

        return true;
    }

    public function paidCommission($request)
    {
        $row = StoresCommission::query()->where('id', $request->id)->first();
        $row->commission = $row->commission - $request->paid;
        $row->save();
    }

    public function listAllStoresCommissions()
    {
        return StoresCommission::query()->select('stores.name', 'stores.id as store_id', 'stores_commissions.commission', 'stores_commissions.id as commission_id')
            ->join('stores', 'stores_commissions.store_id', '=', 'stores.id')
            ->orderBy('stores_commissions.id','DESC')
            ->paginate(10);
    }

    public function listAllCommissions()
    {
        return Commission::query()->orderBy('id','DESC')->paginate(10);
    }

    public static function getCommissionByStoreType($type)
    {
        return Commission::query()->where('store_type_id', $type)->where('active', '=', 1)->first();
    }

    public static function addStoreCommission($store_id, $commission)
    {
        $row = StoresCommission::query()->where('store_id', $store_id)->first();
        if (!$row) {
            $row = new StoresCommission();
        }
        $row->store_id = $store_id;
        $row->commission = ($row) ? $row->commission + $commission : $commission;
        $row->save();
    }

}
