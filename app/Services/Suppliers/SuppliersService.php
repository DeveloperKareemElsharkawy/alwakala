<?php

namespace App\Services\Suppliers;

use App\Repositories\StoreRepository;

class SuppliersService
{
    private $storeRepository;

    public function __construct(
        StoreRepository $storeRepository
    )
    {
        $this->storeRepository = $storeRepository;
    }
    public function isValidSupplierProducts($productIds): bool {
        $user = auth('api')->user();
        $supplierProducts = $this->storeRepository->getSupplierProductsByUserId($user->id, $productIds);
        $invalidProducts = array_diff($productIds, array_column($supplierProducts,'product_id'));
        if (count($invalidProducts)) {
            return  false;
        }
        return true;
    }

}
