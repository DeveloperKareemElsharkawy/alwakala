<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\Activity\Activities;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Events\Logs\DashboardLogs;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Dashboard\Offers\NewOfferRequest;
use App\Lib\Log\ServerError;
use App\Models\Offer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\Offers\OfferResource;
use App\Repositories\OffersRepository;
use App\Services\Offers\OffersService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OffersController extends BaseController
{
    private $offerService;
    protected $model;

    public function __construct(OffersService $offerService , Offer $model)
    {
        $this->offerService = $offerService;
        $this->model = $model;
    }

    public function index(Request $request)
    {
        // $request->user_id = null;
        try {
            $data = $this->offerService->list($request);

            return response()->json([
                "status" => AResponseStatusCode::SUCCESS,
                "message" => "offers retrieved successfully",
                "data" => $data
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in offers of dashboard offers' . __LINE__ . $e);
            // return $e->getMessage();
            return $this->connectionError($e);

        }
    }

    public function show(Request $request, $id)
    {
        // dd($id);
        try {
            // $query = Offer::query();
            // $offers = $query->select('id', 'name', 'activation', 'from', 'to', 'user_id', 'type_id', 'created_at')
            //     ->orderBy('updated_at', 'desc')
            //     ->with('type', 'user', 'offers_products.product')
            //     ->where('id', $id)
            //     ->first();
            $offer = new OfferResource($this->model->findOrFail($id));
            return response()->json([
                "status" => AResponseStatusCode::SUCCESS,
                "message" => "offer retrieved successfully",
                "data" => $offer
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in show of dashboard offer' . __LINE__ . $e);
            return $e->getMessage();
            return $this->connectionError($e);
        }
    }
}
