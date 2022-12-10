<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Lib\Helpers\Lang\LangHelper;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PagesController extends BaseController
{
    /**
     * @var mixed|string
     */
    private $lang;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->lang = LangHelper::getDefaultLang($request);
    }

    /**
     * @return JsonResponse
     *  return Terms And Conditions Page By Slug & APP ID
     */
    public function termsAndConditions()
    {
        try {
            $termsAndConditions = Page::query()->whereSlug('terms-and-conditions')->first();

            return $this->success([
                'message' => trans('messages.general.listed'),
                'data' => $termsAndConditions['content_' . $this->lang]
            ]);
        } catch (\Exception $e) {
            Log::error('error in addComplaints of seller Complaints' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @return JsonResponse
     *  return Privacy Policy Page By Slug & APP ID
     */
    public function privacyPolicy()
    {
        try {
            $privacyPolicy = Page::query()->whereSlug('privacy-policy')->first();

            return $this->success([
                'message' => trans('messages.general.listed'),
                'data' => $privacyPolicy['content_' . $this->lang]
            ]);
        } catch (\Exception $e) {
            Log::error('error in addComplaints of seller Complaints' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @return JsonResponse
     *  return Refund Policy Page By Slug & APP ID
     */
    public function refundPolicy()
    {
        try {
            $refundPolicy = Page::query()->whereSlug('refund-policy')->first();

            return $this->success([
                'message' => trans('messages.general.listed'),
                'data' => $refundPolicy['content_' . $this->lang]
            ]);
        } catch (\Exception $e) {
            Log::error('error in addComplaints of seller Complaints' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @return JsonResponse
     *  return Refund Policy Page By Slug & APP ID
     */
    public function productPolicies()
    {
        try {
            $policies = Page::query()->wherein('slug',['product-non-wekala-policy','product-wekala-policy'])
                ->select('pages.id','pages.name_' . $this->lang . ' as name','pages.content_' . $this->lang . ' as content','policy_id')
                ->get();

            $generalPolicies = Page::query()->whereSlug('product-policy')
                ->select('pages.id','pages.name_' . $this->lang . ' as name','pages.content_' . $this->lang . ' as content')
                ->first();

            return $this->success([
                'message' => trans('messages.general.listed'),
                'data' => [
                    'description' => $generalPolicies->content,
                    'policies' => $policies
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('error in addComplaints of seller Complaints' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @return JsonResponse
     *  return Refund Policy Page By Slug & APP ID
     */
    public function productWekalaPolicy()
    {
        try {
            $refundPolicy = Page::query()->whereSlug('product-wekala-policy')->first();

            return $this->success([
                'message' => trans('messages.general.listed'),
                'data' => $refundPolicy['content_' . $this->lang]
            ]);
        } catch (\Exception $e) {
            Log::error('error in addComplaints of seller Complaints' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @return JsonResponse
     *  return Refund Policy Page By Slug & APP ID
     */
    public function shippingMethodsPolicies()
    {
        try {
            $refundPolicy = Page::query()->whereSlug('shipping-methods-policy')->first();

            return $this->success([
                'message' => trans('messages.general.listed'),
                'data' => $refundPolicy['content_' . $this->lang]
            ]);
        } catch (\Exception $e) {
            Log::error('error in addComplaints of seller Complaints' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @return JsonResponse
     *  return Refund Policy Page By Slug & APP ID
     */
    public function productMaterialsPolicies()
    {
        try {
            $refundPolicy = Page::query()->whereSlug('product-materials-policy')->first();

            return $this->success([
                'message' => trans('messages.general.listed'),
                'data' => $refundPolicy['content_' . $this->lang]
            ]);
        } catch (\Exception $e) {
            Log::error('error in addComplaints of seller Complaints' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
