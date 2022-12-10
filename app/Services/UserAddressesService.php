<?php


namespace App\Services;


use App\Lib\Helpers\UserId\UserId;
use App\Models\UserAddress;
use App\Repositories\UserAddressesRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserAddressesService
{
    /**
     * @var UserAddressesRepository
     */
    private $userAddressRepository;

    /**
     * UserAddressesService constructor.
     * @param UserAddressesRepository $userAddress
     */
    public function __construct(UserAddressesRepository $userAddress)
    {
        $this->userAddressRepository = $userAddress;
    }


    /**
     * Call set user default address function from repository.
     * @param $data
     * @return bool
     */
    public function setDefaultAddress($data): bool
    {
        try {
            DB::beginTransaction();
            $this->userAddressRepository->removeDefaultAddress($data['user_id']);
            $this->userAddressRepository->setUserAddressAsDefault($data);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }

    }
}
