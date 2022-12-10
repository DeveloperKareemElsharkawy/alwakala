<?php

namespace App\Services\TargetOffers;
use App\Models\TargetOffer;
use App\Repositories\StoreRepository;
use App\Repositories\TargetOfferRepository;
use Carbon\Carbon;

class TargetOffersService
{
    private $targetOfferRepository, $storeRepository;

    public function __construct(
        TargetOfferRepository $targetOfferRepository,
        StoreRepository $storeRepository
    )
    {
        $this->targetOfferRepository = $targetOfferRepository;
        $this->storeRepository = $storeRepository;
    }

    public function createTargetOffer($data)
    {
        $user = auth('api')->user();
        $targetOffer = new TargetOffer();
        $targetOffer->name_ar = $data['name_ar'];
        $targetOffer->name_en = $data['name_en'];
        $targetOffer->description = $data['description'];
        $targetOffer->start_date = $data['start_date'];
        $targetOffer->end_date = $data['end_date'];
        $targetOffer->is_active = $data['is_active'];
        $targetOffer->discount_value = $data['discount_value'];
        $targetOffer->owner_user_id = $user->id;
        $targetOffer->start_counting_date = $data['start_counting_date'];
        $targetOffer->save();
        // insert milestones
        $targetMilestones = $this->initializeTargetMilestonesData($data['milestones'], $targetOffer->id);
        $this->targetOfferRepository->insertTargetMilestones($targetMilestones);
        // insert target offer products
        $targetOfferProducts = $this->initializeTargetOfferProductsData($data['products'], $targetOffer->id);
        $this->targetOfferRepository->insertTargetOfferProducts($targetOfferProducts);
        // get affected owners with old stock
        $ownersWithOldStocks = $this->storeRepository->getSellersWithOldStoresProductsStock($user->id,$data['products'], $targetOffer->start_counting_date);
        $ownersWithTotalStockPrices = $this->ownersWithTotalStockPrices($ownersWithOldStocks);
        $lowestMileStone = $targetOffer->milestones()->first();
        // owners who will achieve minimum milestone
        $affectedOwnersWithStock = $this->getAffectedOwnersWithStock($ownersWithTotalStockPrices, $lowestMileStone);
        $affectedOwnerIds = array_column($affectedOwnersWithStock, 'user_id');
        // todo dispatch job to notify affected owners for now
        $targetOfferUsersWithProducts = $this->initializeTargetOfferUsersAndUserProducts($affectedOwnersWithStock, $targetOffer->id);
        // insert affected target_offer_users
        $this->targetOfferRepository->insertTargetOfferUsers($targetOfferUsersWithProducts['target_users']);
        // insert target_receiver_products
        $this->targetOfferRepository->insertTargetReceiversProducts($targetOfferUsersWithProducts['target_user_products']);
    }
    // accept offer api or reject
    // get offers
    // get offer details

    // discount % from consumer_price - price after discount * offer discount in offer table

    private function initializeTargetMilestonesData($milestones, $targetOfferId):array
    {
        $targetMilestones = [];
        foreach ($milestones as $milestone)
        {
            $targetMileStone['target_offer_id'] = $targetOfferId;
            $targetMileStone['targeted_price'] = $milestone['targeted_price'];
            $targetMileStone['reward_value'] = $milestone['reward_value'];
            $targetMileStone['is_active'] = $milestone['is_active'];
            $targetMileStone['created_at'] = Carbon::now();
            $targetMileStone['updated_at'] = Carbon::now();
            $targetMilestones[] = $targetMileStone;
        }
        return $targetMilestones;
    }

   private function initializeTargetOfferProductsData($productIds, $offerId):array
   {
       $targetOfferProducts = [];
       foreach ($productIds as $productId)
        {
            $targetOfferProduct = [];
            $targetOfferProduct['target_id'] = $offerId;
            $targetOfferProduct['product_id'] = $productId;
            $targetOfferProduct['created_at'] = Carbon::now();
            $targetOfferProduct['updated_at'] = Carbon::now();
            $targetOfferProducts[] = $targetOfferProduct;
        }
       return $targetOfferProducts;
   }
   private function ownersWithTotalStockPrices($ownersWithOldStocks) {
        $ownersWithProductsStocks = [];

        foreach ($ownersWithOldStocks as $ownersWithOldStock) {
            if (!isset($ownersWithProductsStocks[$ownersWithOldStock['owner_id']]['products_total_price'])) {
                $ownersWithProductsStocks[$ownersWithOldStock['owner_id']]['products_total_price'] = $ownersWithOldStock['stock'] * $ownersWithOldStock['product_price'];
            }else {
                $ownersWithProductsStocks[$ownersWithOldStock['owner_id']]['products_total_price'] += $ownersWithOldStock['stock'] * $ownersWithOldStock['product_price'];
            }
            if (!isset($ownersWithProductsStocks[$ownersWithOldStock['owner_id']]['products'][$ownersWithOldStock['product_id']]['stock'])) {
                $ownersWithProductsStocks[$ownersWithOldStock['owner_id']]['products'][$ownersWithOldStock['product_id']]['stock'] = $ownersWithOldStock['stock'];
            }else {
                $ownersWithProductsStocks[$ownersWithOldStock['owner_id']]['products'][$ownersWithOldStock['product_id']]['stock'] += $ownersWithOldStock['stock'];
            }
        }
        return $ownersWithProductsStocks;
   }
   private function getAffectedOwnersWithStock($ownersWithTotalStockPrices, $lowestMileStone):array {
        $affectedOwners = [];
        foreach ($ownersWithTotalStockPrices as $userId => $ownersWithTotalStockPrice) {
            if ($ownersWithTotalStockPrice['products_total_price'] >= $lowestMileStone->targeted_price) {
                $ownersWithTotalStockPrice['user_id'] = $userId;
                $affectedOwners[] = $ownersWithTotalStockPrice;
            }
        }
        return $affectedOwners;
   }
   private function initializeTargetOfferUsersAndUserProducts($affectedOwnersWithStocks, $targetOfferId):array {
        $targetUsers = [];
        $targetUsersProducts = [];
        foreach ($affectedOwnersWithStocks as $affectedOwnersWithStock) {
            $targetUser = [];
            $targetUser['target_offer_id'] = $targetOfferId;
            $targetUser['receiver_user_id'] = $affectedOwnersWithStock['user_id'];
            $targetUser['is_approved'] = false;
            $targetUser['created_at'] = Carbon::now();
            $targetUser['updated_at'] = Carbon::now();
            $targetUsers[] = $targetUser;
            foreach ($affectedOwnersWithStock['products'] as $productId => $product) {
                $targetUserProduct = [];
                $targetUserProduct['product_id'] = $productId;
                $targetUserProduct['receiver_user_id'] = $affectedOwnersWithStock['user_id'];
                $targetUserProduct['stock'] = $product['stock'];
                $targetUser['created_at'] = Carbon::now();
                $targetUser['updated_at'] = Carbon::now();
                $targetUsersProducts[] = $targetUserProduct;
            }
        }
        return ['target_users' => $targetUsers, 'target_user_products' => $targetUsersProducts];
   }
   public function validateIfProductsIntersectWithActiveOffer($data):array {
        $intersectedProducts = $this->targetOfferRepository->getIntersectedProducts($data);
        if (count($intersectedProducts)) {
            return ['status'=> true, 'data' => array_unique($intersectedProducts)];
        }
        return ['status'=> false, 'data' => []];
   }
}
