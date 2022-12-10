<?php

namespace App\Http\Controllers\Shipping\SellerApp;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Models\Product;
use App\Models\SellerRate;
use App\Models\Shipping\ShippingCompany;
use App\Models\Shipping\ShippingCompanyLine;
use App\Models\Shipping\ShippingCompanyLocation;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ShippingCompaniesController extends Controller
{
    public function index()
    {
        try {
            $shippingCompanies = ShippingCompany::query()
                ->select('shipping_companies.id', 'name_en', 'name_ar', 'image',
                    DB::raw("(
                    SELECT CASE WHEN SUM(seller_rates.rate) / COUNT(seller_rates.rate) IS NOT NULL
                    THEN
                    CAST( CAST(SUM(seller_rates.rate) AS FLOAT) / CAST( COUNT(seller_rates.rate) AS FLOAT)  AS FLOAT)
                    ELSE 0
                     END  as rate FROM seller_rates WHERE seller_rates.rated_id = shipping_companies.id and rated_type = '" . ShippingCompany::class . "')"))
                ->withCount(['locations', 'lines', 'rate'])
                ->get();

            foreach ($shippingCompanies as $shippingCompany) {
                if ($shippingCompany->image)
                    $shippingCompany->image = config('filesystems.aws_base_url') . $shippingCompany->image;
            }

            return response()->json([
                'status' => true,
                'message' => trans('messages.shipping.shipping_company'),
                'data' => $shippingCompanies
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            return ServerError::handle($e);
        }
    }

    public function getShippingCompanyLocations($id)
    {
        try {
            $shippingCompanyLocations = ShippingCompanyLocation::query()
                ->select('id', 'address', 'latitude', 'longitude', 'shipping_company_id')
                ->where('shipping_company_id', $id)
                ->with('phones')
                ->get();

            $phones = '';
            foreach ($shippingCompanyLocations as $location) {
                foreach ($location->phones as $phone) {
                    $phones .= $phone->phone . ' - ';
                }
                unset($location->phones);
                $location->phones = rtrim($phones, ' - ');
                $phones = '';
            }

            return response()->json([
                'status' => true,
                'message' => trans('messages.shipping.company_location'),
                'data' => $shippingCompanyLocations
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            return ServerError::handle($e);
        }
    }

    public function getShippingCompanyLines($id)
    {
        try {
            $shippingCompanyLocations = ShippingCompanyLine::query()
//                ->select('id', 'address', 'latitude', 'longitude', 'shipping_company_id')
                ->where('shipping_company_id', $id)
                ->with(['price', 'days', 'fromArea', 'toArea'])
                ->get();

            $days = '';
            foreach ($shippingCompanyLocations as $line) {
                $line->from = new \stdClass();
                $line->from->ar = $line->fromArea->area->name_ar;
                $line->from->en = $line->fromArea->area->name_en;
                $line->from->type = $line->fromArea->place_type;
                unset($line->fromArea);
                $line->to = new \stdClass();
                $line->to->ar = $line->toArea->area->name_ar;
                $line->to->en = $line->toArea->area->name_en;
                $line->to->type = $line->toArea->place_type;
                unset($line->toArea);
                foreach ($line->days as $day) {
                    $days .= $day->dayName->name_en . ' - ';
                }
                unset($line->days);
                $line->days = rtrim($days, ' - ');
                $days = '';
            }

            return response()->json([
                'status' => true,
                'message' => trans('messages.shipping.company_lines'),
                'data' => $shippingCompanyLocations
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            return ServerError::handle($e);
        }
    }

    public function rateShippingCompany(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'shipping_company_id' => 'required|numeric|exists:shipping_companies,id',
                'rate' => 'required|numeric|min:1|max:5',
                'review' => 'string|max:255'
            ]);


            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            SellerRate::updateOrCreate(
                ['rater_type' => User::class, 'rater_id' => $request->user_id,
                    'rated_type' => ShippingCompany::class, 'rated_id' => $request->shipping_company_id,],
                ['rate' => $request->rate, 'review' => $request->review]
            );
            return response()->json([
                'status' => true,
                'message' => trans('messages.shipping.company_rated'),
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            return ServerError::handle($e);
        }
    }

    public function suggestedShippingCompany(Request $request)
    {
        try {
            try {
                $shippingCompanies = ShippingCompany::query()
                    ->select('id', 'name_en', 'name_ar', 'image', 'email', 'phone')
                    ->get();

                foreach ($shippingCompanies as $shippingCompany) {
                    if ($shippingCompany->image)
                        $shippingCompany->image = config('filesystems.aws_base_url') . $shippingCompany->image;
                }

                return response()->json([
                    'status' => true,
                    'message' => trans('messages.shipping.shipping_company'),
                    'data' => $shippingCompanies
                ], AResponseStatusCode::SUCCESS);
            } catch (\Exception $e) {
                return ServerError::handle($e);
            }
        } catch (\Exception $e) {
            return ServerError::handle($e);
        }
    }

}
