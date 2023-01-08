<?php


namespace App\Repositories;


use App\Lib\Helpers\Lang\LangHelper;
use App\Models\Address;
use App\Models\City;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class UserAddressesRepository
{
    /**
     * @var Address
     */
    private $userAddress;
    /**
     * @var mixed|string
     */
    private $lang;

    /**
     * UserAddressesRepository constructor.
     * @param Address $userAddress
     * @param Request $request
     */
    public function __construct(Address $userAddress, Request $request)
    {
        $this->userAddress = $userAddress;
        $this->lang = $request->header('lang');
    }


    /**
     * Create user address.
     * @param $data
     * @return bool
     */
    public function createUserAddress($data)
    {
        try {
            $city_id = $data['city_id'];
            $this->userAddress->main_street = $data['street_name'];
            $this->userAddress->latitude = $data['latitude'];
            $this->userAddress->longitude = $data['longitude'];
            $this->userAddress->building_no = $data['building_no'];
            $this->userAddress->landmark = $data['landmark'];
            $this->userAddress->is_default = false;
            $this->userAddress->user_id = $data['user_id'];
            $this->userAddress->city_id = $city_id;
            $this->userAddress->save();
            return true;
        } catch (\Exception $e) {
            return $e;
        }

    }

    /**
     *  Edit user address.
     * @param $data
     * @return bool
     */
    public function editUserAddress($data): bool
    {
        try {
            $address = $this->getUserAddressForEdit($data['address_id']);
            $address->main_street = $data['street_name'];
            $address->latitude = $data['latitude'];
            $address->longitude = $data['longitude'];
            $address->building_no = $data['building_no'];
            $address->landmark = $data['landmark'];
            $address->city_id = $data['city_id'];
            $address->save();
            return true;
        } catch (\Exception $e) {

            return false;
        }


    }

    /**
     * Delete user address.
     * @param $id
     * @return bool
     */
    public function deleteUserAddress($id): bool
    {
        try {
            $this->userAddress->query()->where('id', $id)->delete();
            return true;
        } catch (\Exception $e) {
            return false;
        }

    }

    /**
     * get user address.
     * @param $id
     * @param string $lang
     * @return false|Builder|Model|object|null
     */
    public function getUserAddress($id, $lang = 'ar')
    {
        try {
            return $this->userAddress->query()
                ->select(['addresses.id', 'addresses.main_street as street_name', 'addresses.latitude', 'addresses.longitude', 'addresses.is_default', 'addresses.building_no', 'addresses.landmark'
                    , "cities.name_$lang as city_name"
                    , "states.name_$lang as region_name"
                ])
                ->where('addresses.id', $id)
                ->leftJoin('cities', 'cities.id', '=', 'addresses.city_id')
                ->leftJoin('states', 'states.id', '=', 'cities.state_id')
                ->get();
        } catch (\Exception $e) {
            return false;
        }
    }


    /**
     * Edit user addresses.
     * @param $userId
     * @param string $lang
     * @return false|Builder[]|Collection
     */
    public function getUserAddresses($userId, $lang = 'ar')
    {
        try {
            return $this->userAddress->query()
                ->select(['addresses.id', 'main_street as street', 'building_no', 'addresses.latitude', 'addresses.longitude', 'is_default', 'landmark'
                    , "cities.name_$lang as city_name"
                    , "states.name_$lang as region_name"
                ])
                ->where('user_id', $userId)
                ->leftJoin('cities', 'cities.id', '=', 'addresses.city_id')
                ->leftJoin('states', 'states.id', '=', 'cities.state_id')
                ->orderBy('id', 'desc')
                ->get();

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Set user default address.
     * @param $data
     * @return bool
     */
    public function setUserAddressAsDefault($data): bool
    {
        try {

            $address = $this->getUserAddressForEdit($data['address_id']);
            $address->is_default = true;
            $address->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }

    }

    /**
     * Remove user default address.
     * @param $userId
     * @return bool
     */
    public function removeDefaultAddress($userId): bool
    {
        try {
            $oldDefaultAddress = $this->userAddress->query()
                ->where('user_id', $userId)
                ->where('is_default', true)
                ->first();
            if ($oldDefaultAddress) {
                $oldDefaultAddress->is_default = false;
                $oldDefaultAddress->save();

            }
            return true;
        } catch (\Exception $e) {
            return false;
        }

    }

    /**
     * @param $addressId
     * @param $userId
     * @return bool
     */
    public function checkIfAddressBelongsToUser($addressId, $userId): bool
    {
        try {
            $address = $this->userAddress->query()
                ->select(['id'
                ])
                ->where('id', $addressId)
                ->where('user_id', $userId)
                ->first();
            if ($address) {
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function getUserAddressForEdit($addressId)
    {
        return $this->userAddress->query()->where('id', $addressId)->first();
    }
}
