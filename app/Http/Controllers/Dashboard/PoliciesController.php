<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Exports\Dashboard\PoliciesExport;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Policies\CheckPolicyExistenceRequest;
use App\Http\Requests\Policies\CreatePolicyRequest;
use App\Http\Requests\Policies\FilterDataRequest;
use App\Http\Requests\Policies\UpdatePolicyRequest;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Log\ValidationError;
use App\Models\Policy;
use App\Repositories\PolicyRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class PoliciesController extends BaseController
{
    private $policiesRepository;
    /**
     * @var string
     */
    private $lang;

    public function __construct(PolicyRepository $policiesRepository,Request $request)
    {
        $this->policiesRepository = $policiesRepository;
        $this->lang = LangHelper::getDefaultLang($request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(FilterDataRequest $request)
    {
        try {
            $policiesList = $this->policiesRepository->list($request);

            return response()->json([
                'status' => true,
                'message' => 'Policies',
                'data' => $policiesList['data'],
                'offset' => (int)$policiesList['offset'],
                'limit' => (int)$policiesList['limit'],
                'total' => $policiesList['count'],
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in index of dashboard policy' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }


    /**
     * @param CheckPolicyExistenceRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function show(CheckPolicyExistenceRequest $request, $id)
    {
        try {
            $policy = $this->policiesRepository->showById($id);

            return $this->success([
                'success' => true,
                'message' => 'Policy',
                'data' => $policy
            ]);
        } catch (\Exception $e) {
            Log::error('error in show of dashboard Policy' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param CreatePolicyRequest $request
     * @return JsonResponse
     */
    public function store(CreatePolicyRequest $request)
    {
        try {
            $policy = $this->policiesRepository->create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Policy Created',
                'data' => $policy
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in store of dashboard policy' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param UpdatePolicyRequest $request
     * @return JsonResponse
     */
    public function update(UpdatePolicyRequest $request)
    {
        try {
            $this->policiesRepository->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Policy Updated',
                'data' => []
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in store of dashboard policy' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @return JsonResponse
     */
    public function destroy(CheckPolicyExistenceRequest $request, $id)
    {
        try {
            $this->policiesRepository->deletePolicy($id);

            return response()->json([
                'success' => true,
                'message' => 'Policy Deleted',
                'data' => []
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in store of dashboard policy' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function export(Request $request)
    {
        try {
            return  Excel::download(new PoliciesExport($request), 'policies.xlsx');
        } catch (\Exception $e) {
            Log::error('error in ORders in dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
    public function getPolicies(Request $request)
    {
        try {
            $policies = Policy::query()
                ->select('id', 'name_' . $this->lang, 'description_' . $this->lang)
                ->get();
            return response()->json([
                'status' => true,
                'message' => trans('messages.policy.get_policies'),
                'data' => $policies
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in getPolicies of seller Policies' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
