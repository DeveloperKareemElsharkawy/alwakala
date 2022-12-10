<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Models\Category;
use App\Models\Visitor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class VisitorsController extends BaseController
{
    private $lang;

    public function __construct(Request $request)
    {
        $this->lang = LangHelper::getDefaultLang($request);
    }

    public function RegisterVisitor(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'device_token' => 'required|unique:visitors,device_token',
                'store_type_id' => 'required|exists:store_types,id',
                'categories' => 'required|array',
                'categories.*' => 'required|numeric|exists:categories,id',
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            DB::beginTransaction();

            $visitor = new Visitor;
            $visitor->device_token = $request->device_token;
            $visitor->store_type_id = $request->store_type_id;
            $visitor->save();
            $visitor->visitorCategories()->attach($request->categories);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => trans('messages.visitors.visitor_data_added'),
                'data' => $visitor
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in RegisterVisitor of seller Visitors' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getCategories()
    {
        try {
            $categories = Category::query()
                ->select('id', 'name_' . $this->lang . ' as name', 'category_id')
                ->whereNull('category_id')
                ->where('activation', true)
                ->with(['categories' => function ($q) {
                    $q->select('id', 'name_' . $this->lang . ' as name', 'category_id');
                }])
                ->get();

            return response()->json([
                'success' => true,
                'message' => trans('messages.visitors.visitor_categories'),
                'data' => $categories
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in getCategories of seller Visitors' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

}
