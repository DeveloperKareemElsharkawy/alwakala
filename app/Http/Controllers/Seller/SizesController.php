<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Http\Resources\Seller\Sizes\SizesResource;
use App\Lib\Helpers\Categories\CategoryHelper;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Models\PackingUnit;
use App\Models\Size;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SizesController extends BaseController
{
    public function getSizes(Request $request)
    {
        try {
             if (!$request->category_id || !CategoryHelper::checkCategoryLevel('sub_sub_category', $request->category_id)) {
                return $this->error(['message' => trans('messages.category.un_valid_parent')]);
            }
            $query = Size::query()->with('categories');
            if ($request->filled('category_id')) {
                $query->whereHas('categories', function ($query) use ($request) {
                    $query->where("categories.id", $request->category_id);
                });
            }
            $sizes = $query->get();
            if(!count($sizes))
                return $this->error(['message' => trans('messages.category.un_valid_parent')]);

            return $this->success(['message' => trans('messages.sections.sizes'),'data'=>SizesResource::collection($sizes)]);
        } catch (\Exception $e) {
            Log::error('error in getSizes of seller sizes' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
