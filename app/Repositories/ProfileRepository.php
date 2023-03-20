<?php


namespace App\Repositories;


use App\Http\Controllers\Controller;
use App\Http\Resources\Seller\Store\StoreOpeningHoursResource;
use App\Lib\Helpers\Rate\RateHelper;
use App\Lib\Helpers\StoreId\StoreId;
use App\Lib\Helpers\UserId\UserId;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\CategoryStore;
use App\Models\DaysOfWeek;
use App\Models\FollowedStore;
use App\Models\PackingUnitProduct;
use App\Models\SellerRate;
use App\Models\Store;
use App\Models\StoreOpeningHour;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProfileRepository extends Controller
{

    public function getStoreProfile($userId, $lang)
    {
        $storeProfile = Store::query()
            ->select(
                'stores.*',
                'cities.name_' . $lang . ' as city_name',
                'states.id as state_id', 'states.name_' . $lang . ' as state_name',
                'countries.id as country_id', 'countries.name_' . $lang . ' as country_name'
            )
            ->where('stores.user_id', $userId)
            ->with('sellerRate')
            ->with('openHours')
            ->with('storeImages')
            ->join('cities', 'cities.id', '=', 'stores.city_id')
            ->join('states', 'states.id', '=', 'cities.state_id')
            ->join('regions', 'regions.id', '=', 'states.region_id')
            ->join('countries', 'countries.id', '=', 'regions.country_id')
            ->first();
        if (count($storeProfile->SellerRate) > 0) {
            $storeProfile->rate = $storeProfile->SellerRate[0]->rate;
            $storeProfile->comments_count = SellerRate::query()
                ->where('rated_id', $storeProfile->id)
                ->where('rated_type', Store::class)
                ->whereNotNull('review')
                ->count();
        } else {
            $storeProfile->rate = 0;
            $storeProfile->comments = 0;
        }
        unset($storeProfile->sellerRate);
        $storeProfile->following = FollowedStore::query()
            ->where('store_id', $storeProfile->id)
            ->count();
        $storeCategories = CategoryStore::query()
            ->select('category_id')
            ->with('category')
            ->where('store_id', $storeProfile->id)
            ->get();
        $categories = [];
        foreach ($storeCategories as $storeCategory) {
            $categories[] = $storeCategory->category;
        }
        $storeProfile->categories = $categories;

        if ($storeProfile->logo) {
            $storeProfile->logo = config('filesystems.aws_base_url') . $storeProfile->logo;
        }
        if ($storeProfile->licence) {
            $storeProfile->licence = config('filesystems.aws_base_url') . $storeProfile->licence;
        }
        if ($storeProfile->cover) {
            $storeProfile->cover = config('filesystems.aws_base_url') . $storeProfile->cover;
        }
        foreach ($storeProfile->storeImages as $image) {
            $image->image = config('filesystems.aws_base_url') . $image->image;
        }
        $storeProfile->vacation = $this->getVacationDays($storeProfile->id, $lang);
        $storeProfile->rates = RateHelper::storeRatesLimited($storeProfile->id, Store::class);
        return $storeProfile;
    }

    public function getStoreProfileForVisitors($userId, $lang, $storeId)
    {
        $storeProfile = Store::query()
            ->select(
                'stores.*',
                'cities.name_' . $lang . ' as city_name',
                'states.id as state_id', 'states.name_' . $lang . ' as state_name',
                'countries.id as country_id', 'countries.name_' . $lang . ' as country_name',
                'store_settings.website',
                'store_settings.facebook',
                'store_settings.instagram',
                'store_settings.whatsapp',
                'store_settings.twitter',
                'store_settings.pinterest',
                'store_settings.youtube'
            )
            ->where('stores.id', $storeId)
            ->with(['storeImages', 'sellerRate'=> function($query){
                return $query->take(3);
            }])
            ->join('cities', 'cities.id', '=', 'stores.city_id')
            ->join('states', 'states.id', '=', 'cities.state_id')
            ->join('regions', 'regions.id', '=', 'states.region_id')
            ->join('countries', 'countries.id', '=', 'regions.country_id')
            ->leftJoin('store_settings', 'stores.id', '=', 'store_settings.store_id')
            ->first();
        if (count($storeProfile->SellerRate) > 0) {
            $storeProfile->rate = $storeProfile->SellerRate[0]->rate;
            $storeProfile->comments_count = SellerRate::query()
                ->where('rated_id', $storeProfile->id)
                ->where('rated_type', Store::class)
                ->whereNotNull('review')
                ->count();
        } else {
            $storeProfile->rate = 0;
            $storeProfile->comments = 0;
        }

        $storeProfile->following = FollowedStore::query()
            ->where('store_id', $storeProfile->id)
            ->count();
        $storeCategories = CategoryStore::query()
            ->select('category_id')
            ->with('mainCategory')
            ->where('store_id', $storeProfile->id)
            ->get();
        $categories = [];
        foreach ($storeCategories as $storeCategory) {
            $categories[] = $storeCategory->category;
        }
        $storeProfile->categories = $categories;

        if ($storeProfile->logo)
            $storeProfile->logo = config('filesystems.aws_base_url') . $storeProfile->logo;
        if ($storeProfile->licence)
            $storeProfile->licence = config('filesystems.aws_base_url') . $storeProfile->licence;
        if ($storeProfile->cover)
            $storeProfile->cover = config('filesystems.aws_base_url') . $storeProfile->cover;

        foreach ($storeProfile->storeImages as $image) {
            $image->image = config('filesystems.aws_base_url') . $image->image;
        }
        $storeProfile->working_days = $this->getWorkingDays($storeProfile->id, $lang);
        $storeProfile->rates = RateHelper::storeRatesLimited($storeProfile->id, Store::class);
        if (!is_null($userId)) {
            $isFollow = FollowedStore::query()
                ->where('user_id', $userId)
                ->where('store_id', $storeId)
                ->first();
            if ($isFollow) {
                $storeProfile->is_follow = true;
            } else {
                $storeProfile->is_follow = false;
            }
        } else {
            $storeProfile->is_follow = false;
        }
        return $storeProfile;
    }


    public function getVacationDays($storeId, $lang)
    {
        $weekDays = [1, 2, 3, 4, 5, 6, 7];
        $workDays = StoreOpeningHour::query()->where('store_id', $storeId)->pluck('days_of_week_id')->toArray();
        $vacation = array_diff($weekDays, $workDays);
        $array = array_values($vacation);
        return DaysOfWeek::query()
            ->select('id', 'name_' . $lang . ' as name')
            ->whereIn('id', $array)->get();
    }

    public function getWorkingDays($storeId, $lang)
    {
        $workDays = StoreOpeningHour::query()->with('day')->where('store_id', $storeId)->get();

        return StoreOpeningHoursResource::collection($workDays);

    }

    private function convertNumberToWord($number)
    {
        $wordNumber = '';
        switch ($number) {
            case 5:
                $wordNumber = 'Five';
                break;
            case 6;
                $wordNumber = 'Six';
                break;
            case 7;
                $wordNumber = 'Seven';
                break;
        }
        return $wordNumber;
    }

    /**
     * Upload store documents.
     * @param $data
     * @return bool
     */
    public function uploadStoreDocument($data): bool
    {
        try {
            $userId = UserId::UserId($data);
            $storeId = StoreId::getStoreID($data);
            $store = $this->getStoreForUpdate($storeId, $userId);
            if (empty($store)) {
                return false;
            }
            if ($data['logo']) {
                $store->logo = UploadImage::uploadImageToStorage($data['logo'], 'stores');
            }
            if ($data['licence']) {
                $store->licence = UploadImage::uploadImageToStorage($data['licence'], 'stores');
            }
            if ($data['cover']) {
                $store->cover = UploadImage::uploadImageToStorage($data['cover'], 'stores');
            }
            $store->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }

    }

    /**
     * Upload getStoreDocument documents.
     * @param $data
     */
    public function getStoreDocument($data)
    {
        try {
            $userId = UserId::UserId($data);
            $store = Store::query()
                ->where('stores.user_id', $userId)
                ->first();
            return $store;
        } catch (\Exception $e) {
            return null;
        }

    }

    /**
     * Get store object to update it.
     * @param $storeId
     * @param $userId
     * @return Builder|Model|object|null
     */
    private function getStoreForUpdate($storeId, $userId)
    {
        return Store::query()
            ->where('id', $storeId)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Get store object to update it.
     * @param $storeId
     * @param $userId
     * @return Builder|Model|object|null
     */
    function checkConfirmationAccount($userId): bool
    {
        $store = Store::query()
            ->where('user_id', $userId)
            ->first();
        if ($store && $store->is_verified) {
            return true;
        } else {
            return false;
        }
    }
}
