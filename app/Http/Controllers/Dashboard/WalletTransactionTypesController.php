<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Wallet\TranactionTypes\NewTransactionTypeRequest;
use App\Services\Wallet\WalletTransactionTypeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WalletTransactionTypesController extends Controller
{
    public $transactionTypeService;

    public function __construct(WalletTransactionTypeService $transactionTypeService)
    {
        $this->transactionTypeService = $transactionTypeService;
    }

    public function index()
    {

    }

    public function store(NewTransactionTypeRequest $request)
    {
        try {
            $data = $request->all();
            dd($data);
            $this->transactionTypeService->addTransactionType($request->all);
            return response()->json([
                'status' => true,
                'message' => 'created Successfully',
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in getBundles of seller Bundles' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function show(NewTransactionTypeRequest $request)
    {

    }

    public function update(NewTransactionTypeRequest $request)
    {

    }

    public function delete(NewTransactionTypeRequest $request)
    {

    }
}
