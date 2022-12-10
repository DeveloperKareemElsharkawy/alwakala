<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Seller\Faq\FaqCategoriesResource;
use App\Http\Resources\Seller\Faq\FaqResource;
use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FaqsController extends BaseController
{

    /**
     * @return JsonResponse
     *  Return All Faqs Categories
     */
    public function categories(): JsonResponse
    {
        try {
            $faqCategories = FaqCategory::query()->orderBy('order')->get();

            return $this->success(['data' => FaqCategoriesResource::collection($faqCategories)]);
        } catch (\Exception $e) {
            Log::error('error in addComplaints of seller Complaints' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @return JsonResponse
     *  Return All Faqs Categories
     */
    public function faqs($faqCategoryId): JsonResponse
    {
        try {
            $faqCategories = Faq::query()->where('faq_category_id', $faqCategoryId)->get();

            if (!count($faqCategories))
                return $this->notFound();

            return $this->success(['data' => FaqResource::collection($faqCategories)]);
        } catch (\Exception $e) {
            Log::error('error in addComplaints of seller Complaints' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
