<?php


namespace App\Lib\Helpers\Address;


use App\Models\Address;
use App\Models\Store;

class AddressHelper
{

    public static function getDefaultAddress($userId)
    {
        $address = Address::query()
            ->select('id')
            ->where('user_id', $userId)
            ->where('is_default', true)
            ->first();
        if (!$address) {
            return null;
        }
        return $address->id;
    }

    public static function getAddressById($userId,$addressId)
    {
        return Address::query()
             ->where('user_id', $userId)
            ->where('id', $addressId)
            ->first();
    }

    public static function getFullAddress($userId, $lang)
    {
        $address = Address::query()
            ->select('addresses.user_id',
                'addresses.name',
                'addresses.address',
                'addresses.latitude',
                'addresses.longitude',
                'addresses.building_no',
                'addresses.landmark',
                'addresses.main_street',
                'addresses.side_street',
                'addresses.city_id',
                'addresses.mobile',
                'cities.name_' . $lang . ' as city_name',
                'states.id as state_id', 'states.name_' . $lang . ' as state_name',
                'countries.id as country_id', 'countries.name_' . $lang . ' as country_name')
            ->with('city')
            ->where('addresses.user_id', $userId)
            ->where('addresses.is_default', true)
            ->join('cities', 'cities.id', '=', 'addresses.city_id')
            ->join('states', 'states.id', '=', 'cities.state_id')
            ->join('regions', 'regions.id', '=', 'states.region_id')
            ->join('countries', 'countries.id', '=', 'regions.country_id')
            ->first();

        if ($address) {
            $name = 'name_' . $lang;
            $city = $address->city[$name];
            unset($address->city);
            $address->city = $city;
        } else {
            $store = Store::query()
                ->select('stores.user_id',
                    'stores.address',
                    'stores.latitude',
                    'stores.longitude',
                    'stores.building_no',
                    'stores.landmark',
                    'stores.main_street',
                    'stores.side_street',
                    'stores.city_id',
                    'stores.mobile',
                    'cities.name_' . $lang . ' as city_name',
                    'states.id as state_id', 'states.name_' . $lang . ' as state_name',
                    'countries.id as country_id', 'countries.name_' . $lang . ' as country_name'
                )
                ->with('city')
                ->where('stores.user_id', $userId)
                ->join('cities', 'cities.id', '=', 'stores.city_id')
                ->join('states', 'states.id', '=', 'cities.state_id')
                ->join('regions', 'regions.id', '=', 'states.region_id')
                ->join('countries', 'countries.id', '=', 'regions.country_id')
                ->first();
            $store->name = 'Store Address';
            $name = 'name_' . $lang;
            $city = $store->city[$name];
            unset($store->city);
            $store->city = $city;
            return $store;
        }
        return $address;
    }

}
