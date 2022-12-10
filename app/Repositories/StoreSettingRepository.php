<?php


namespace App\Repositories;


use App\Http\Controllers\Controller;
use App\Models\StoreSetting;

class StoreSettingRepository extends Controller
{
    public static function store($data)
    {
        try {
            StoreSetting::query()->updateOrCreate(
                ['store_id' => $data['store_id']],
                [
                    'website' => $data['website'],
                    'facebook' => $data['facebook'],
                    'instagram' => $data['instagram'],
                    'whatsapp' => $data['whatsapp'],
                    'twitter' => $data['twitter'],
                    'pinterest' => $data['pinterest'],
                    'youtube' => $data['youtube'],
                ]
            );
            return true;
        } catch (\Exception $e) {
            info('error on adding store setting' . $e);
            return false;
        }
    }

    public static function list($storeId)
    {
        try {
            return StoreSetting::query()
                ->where('store_id', $storeId)
                ->select('website', 'facebook', 'instagram', 'whatsapp', 'twitter', 'pinterest')
                ->first();
        } catch (\Exception $e) {
            info('error on listing store setting' . $e);
            return false;
        }
    }
}
