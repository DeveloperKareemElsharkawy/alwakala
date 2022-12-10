<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Http\Requests\SellerApp\TargetOffers\CreateTargetOffersRequest;
use App\Services\Suppliers\SuppliersService;
use App\Services\TargetOffers\TargetOffersService;
use http\Env\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TargetOffersController extends BaseController
{
    private $targetOfferService, $suppliersService;

    public function __construct(
        TargetOffersService $targetOfferService,
        SuppliersService    $suppliersService
    )
    {
        $this->targetOfferService = $targetOfferService;
        $this->suppliersService = $suppliersService;
    }

    public function store(CreateTargetOffersRequest $request)
    {
        try {
            $data = $request->all();
            DB::beginTransaction();
            $isValidProducts = $this->suppliersService->isValidSupplierProducts($data['products']);
            if (!$isValidProducts) {
                return response()->json([
                    "status" => AResponseStatusCode::FORBIDDEN,
                    "message" => "You can't create offer on this products, not your products",
                ], AResponseStatusCode::FORBIDDEN);
            }
            $productsIntersectedWithActiveOfferResponse = $this->targetOfferService->validateIfProductsIntersectWithActiveOffer($data);
            if ($productsIntersectedWithActiveOfferResponse['status']) {
                return response()->json([
                    "status" => AResponseStatusCode::FORBIDDEN,
                    "message" => "The following products ids already exist in active offers " . implode(',', $productsIntersectedWithActiveOfferResponse['data']),
                ], AResponseStatusCode::FORBIDDEN);
            }
            $this->targetOfferService->createTargetOffer($data);
            DB::commit();
            return response()->json([
                "status" => AResponseStatusCode::CREATED,
                "message" => 'Offer created Successfully'
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in getForSelection of seller Units' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function takeAction(Request $request, $offer_id)
    {
        try {
            //TODO CONFIRM OR REJECT OFFER
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in getForSelection of seller Units' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
