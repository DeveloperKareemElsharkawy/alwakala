<?php

namespace App\Services\Coupons;

use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\CouponDiscount;
use App\Models\couponProduct;
use App\Models\Product;
use App\Repositories\CouponRepository;
use Carbon\Carbon;

class CouponsService
{
    private $couponRepository;

    public function __construct(CouponRepository $couponRepository)
    {
        $this->couponRepository = $couponRepository;
    }

    public function create($data)
    {
        $coupon = $this->couponRepository->create($data);
        return $coupon;
    }

    public function edit($data)
    {
        $coupon = $this->couponRepository->update($data);
        return $coupon;
    }

    public function list($data): array
    {
        return $this->couponRepository->list($data);
    }

    public function listAll($data): array
    {
        return $this->couponRepository->listAll($data);
    }

    public function show($data, $id)
    {
        return $this->couponRepository->showById($data, $id);
    }

    public function activate($data, $id)
    {
        return $this->couponRepository->activate($data, $id);
    }

    public function activatedCoupons($data)
    {
        return $this->couponRepository->activatedCoupons($data);
    }

    public function inactiveCoupons($data)
    {
        return $this->couponRepository->inactiveCoupons($data);
    }
}
