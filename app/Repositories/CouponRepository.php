<?php

namespace App\Repositories;

use App\Enums\UserTypes\UserType;
use App\Http\Resources\Seller\Coupons\CouponResource;
use App\Lib\Helpers\StoreId\StoreId;
use App\Models\Coupon;
use App\Models\CouponDiscount;
use App\Models\Store;
use Illuminate\Support\Facades\DB;

class CouponRepository
{
    protected $model;

    public function __construct(Coupon $model)
    {
        $this->model = $model;
    }

    public function list($data): array
    {
        $objects = $this->model->query()
            ->where('seller_id', $data['user_id'])->orderBy('id', 'desc')->paginate(config('dashboard.pagination_limit'));
        // dd($objects);

        return CouponResource::collection($objects)->response()->getData(true);
    }

    public function listAll($data): array
    {
        $objects = $this->model->query()
            ->orderBy('id', 'desc')->paginate(config('dashboard.pagination_limit'));

        return CouponResource::collection($objects)->response()->getData(true);
    }


    public function create($data)
    {
        $coupon = Coupon::create($data);

        $storeId = Store::query()->select('id')->where('user_id', $data['user_id'])->first()->id;

        $coupon->products()->syncWithPivotValues($data['products'], ['store_id' => $storeId]);

        foreach ($data['discounts'] as $discount) {
            $couponDiscount = new CouponDiscount;
            $couponDiscount->coupon_id = $coupon->id;
            $couponDiscount->amount_from = $discount['amount_from'];
            $couponDiscount->amount_to = $discount['amount_to'];
            $couponDiscount->discount_type = $discount['discount_type'];
            $couponDiscount->discount = $discount['discount'];
            $couponDiscount->save();
        }

        return new CouponResource($coupon);
    }

    public function update($data)
    {
        $coupon = Coupon::find($data['id']);

        $coupon->update($data);

        $coupon->products()->sync($data['products']);

        CouponDiscount::query()->where('coupon_id', $coupon->id)->forceDelete();

        foreach ($data['discounts'] as $discount) {
            $couponDiscount = new CouponDiscount;
            $couponDiscount->coupon_id = $coupon->id;
            $couponDiscount->amount_from = $discount['amount_from'];
            $couponDiscount->amount_to = $discount['amount_to'];
            $couponDiscount->discount_type = $discount['discount_type'];
            $couponDiscount->discount = $discount['discount'];
            $couponDiscount->save();
        }

        return new CouponResource($coupon);
    }

    public function showById($data, $id)
    {
        return new CouponResource($this->model->newQuery()
            ->with(['products', 'user'])
            ->where('id', $id)
            ->where('seller_id', $data->query('user_id'))
            ->first());
    }

    public function activate($data, $id)
    {
        $coupon = Coupon::where('seller_id', $data['seller_id'])->where('id', $id)->first();

        $coupon->change_active();

        return new CouponResource($coupon);
    }

    public function activatedCoupons($data)
    {
        $objects = $this->model->query()
            ->where('seller_id', $data->query('user_id'))->where('active', 1)->orderBy('id', 'desc')->paginate(config('dashboard.pagination_limit'));

        return CouponResource::collection($objects)->response()->getData(true);
    }

    public function inactiveCoupons($data)
    {
        $objects = $this->model->query()
            ->where('seller_id', $data->query('user_id'))->where('active', 0)->orderBy('id', 'desc')->paginate(config('dashboard.pagination_limit'));

        return CouponResource::collection($objects)->response()->getData(true);
    }
}
