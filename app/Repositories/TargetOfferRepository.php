<?php

namespace App\Repositories;

use App\Models\OrderProduct;
use App\Models\TargetOfferMilestone;
use App\Models\TargetOfferProduct;
use App\Models\TargetOfferUser;
use App\Models\TargetReceiverProduct;

class TargetOfferRepository
{
    public function insertTargetMilestones($milestones)
    {
        TargetOfferMilestone::query()->insert($milestones);
    }

    public function insertTargetOfferProducts($targetOfferProducts)
    {
        TargetOfferProduct::query()->insert($targetOfferProducts);
    }

    public function insertTargetOfferUsers($targetOfferUsers)
    {
        TargetOfferUser::query()->insert($targetOfferUsers);
    }

    public function insertTargetReceiversProducts($targetReceiverProducts)
    {
        TargetReceiverProduct::query()->insert($targetReceiverProducts);
    }

    public function getIntersectedProducts($data)
    {
        return TargetOfferProduct::query()
            ->join('target_offers', 'target_offers.id', '=', 'target_offer_products.target_id')
            ->where('target_offers.is_active', true)
            ->whereIn('target_offer_products.product_id', $data['products'])
            ->whereDate('target_offers.start_counting_date', '>=', date($data['start_counting_date']))
            ->whereDate('target_offers.start_date', '<=', date($data['start_date']))
            ->pluck('target_offer_products.product_id')->toArray();

    }
}
