<?php

namespace App\Http\Controllers\Shipping;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\Shipping\ShippingArea;
use App\Models\Shipping\ShippingCompany;
use App\Models\Shipping\ShippingCompanyLine;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ShippingCompaniesController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = ShippingCompany::query()
                ->select('id', 'name_ar', 'name_en', 'activation', 'email');
            if ($request->filled("query")) {
                $searchQuery = "%" . $request->get("query") . "%";
                $query->where('name_en', "ilike", $searchQuery)
                    ->orWhere('name_ar', "ilike", $searchQuery);
            }
            $shippingCompanies = $query->get();

            return response()->json([
                'status' => true,
                'message' => trans('messages.shipping.shipping_company'),
                'data' => $shippingCompanies
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            return ServerError::handle($e);
        }
    }

    public function show($id)
    {
        try {
            $shippingCompany = ShippingCompany::query()
                ->select('shipping_companies.id',
                    'shipping_companies.name_en',
                    'shipping_companies.name_ar',
                    'shipping_companies.image',
                    'shipping_companies.email',
                    'shipping_companies.activation')
                ->with('locations')
                ->with('lines')
                ->where('shipping_companies.id', $id)
                ->first();

            $days = '';
            foreach ($shippingCompany->lines as $line) {
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

            $phones = '';
            foreach ($shippingCompany->locations as $location) {
                foreach ($location->phones as $phone) {
                    $phones .= $phone->phone . ' - ';
                }
                unset($location->phones);
                $location->phones = rtrim($phones, ' - ');
                $phones = '';
            }

            $shippingCompany->image = config('filesystems.aws_base_url') . $shippingCompany->image;
            if (!$shippingCompany) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.shipping.company_noy_exists'),
                    'data' => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }

            return response()->json([
                'status' => true,
                'message' => trans('messages.shipping.shipping_company'),
                'data' => $shippingCompany
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            return ServerError::handle($e);
        }
    }

    public function createShippingCompany(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name_en' => 'required|string|max:255',
                'name_ar' => 'required|string|max:255',
                'activation' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
                'email' => 'required|email|max:255',
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            $shippingCompany = new ShippingCompany;
            $shippingCompany->name_en = $request->name_en;
            $shippingCompany->name_ar = $request->name_ar;
            $shippingCompany->activation = $request->activation;
            $shippingCompany->email = $request->email;
            $shippingCompany->image = UploadImage::uploadImageToStorage($request->image, 'shipping_companies');
            $shippingCompany->save();

            return response()->json([
                'status' => true,
                'message' => trans('messages.shipping.shipping_company_created'),
                'data' => $shippingCompany
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            return ServerError::handle($e);
        }
    }

    public function delete($id)
    {
        try {
            $shippingCompany = ShippingCompany::query()->where('id', $id)->first();
            if (!$shippingCompany) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.shipping.company_noy_exists'),
                    'data' => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }

            $shippingCompany->delete();

            return response()->json([
                'status' => true,
                'message' => trans('messages.shipping.company_deleted'),
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            return ServerError::handle($e);
        }
    }
}
