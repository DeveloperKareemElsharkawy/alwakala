<?php

namespace App\Http\Controllers\Seller;


use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Http\Requests\SellerApp\Auth\UpdateSellerInfoRequest;
use App\Http\Requests\SellerApp\Auth\UpdateSellerMobileRequest;
use App\Http\Requests\SellerApp\Store\ChangeMobileNumberRequest;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\User;
use App\Models\UserMobileChanges;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SellersController extends BaseController
{

    public function addSellerImage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            $user = User::query()
                ->where('id', $request->user_id)
                ->first();

            if ($user->image) {
                Storage::disk('s3')->delete($user->image);
            }
            $user->image = UploadImage::uploadImageToStorage($request->image, 'sellers');
            $user->save();

            return response()->json([
                'status' => true,
                'message' => trans('messages.stores.image_added'),
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in addSellerImage of seller sellers ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param UpdateSellerInfoRequest $request
     * @return JsonResponse
     */
    public function update(UpdateSellerInfoRequest $request)
    {
        try {
            $seller = User::query()->find($request->user_id);
            $seller->name = $request->name;
            $seller->email = $request->email;
//            $seller->mobile = $request->mobile;
            $seller->save();
            return $this->success(['message' => trans('messages.auth.user_updated')]);
        } catch (\Exception $e) {
            Log::error('error in Update Seller info' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param UpdateSellerMobileRequest $request
     * @return JsonResponse
     *
     */
    public function sendUpdateMobileCode(UpdateSellerMobileRequest $request)
    {
        $userId = $request->user_id;

        $confirmCode = mt_rand(0, 8) . mt_rand(1, 9) . mt_rand(10, 90);

        UserMobileChanges::query()->firstOrCreate(
            ['user_id' => $userId, 'mobile' => $request->mobile],
            ['confirm_code' => $confirmCode]
        );

        return $this->success(['message' => 'Code Sent', 'data' => [
            'confirm_code' => $confirmCode
        ]]);
    }


    /**
     * @param UpdateSellerMobileRequest $request
     * @return JsonResponse
     */
    public function updateMobileConfirmation(UpdateSellerMobileRequest $request)
    {
        $userId = $request->user_id;

        User::query()->where('id', $userId)->update(['mobile' => $request->mobile]);

        return $this->success(['message' => 'Mobile Updated']);
    }


    public function show(Request $request)
    {
        try {
            $seller = User::query()
                ->select('id', 'name', 'email', 'mobile')
                ->find($request->user_id);
            return $this->success(['message' => trans('messages.general.listed'), 'data' => $seller]);
        } catch (\Exception $e) {
            Log::error('error in Update Seller info' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @return JsonResponse
     */
    public function generateBarcode(Request $request)
    {
        $generatedBarcode = mt_rand(0, 8) . mt_rand(1, 9) . mt_rand(90, 900). mt_rand(10, 90);

        return $this->success(['message' => 'Code Sent', 'data' => [
            'generated_barcode' => $generatedBarcode
        ]]);
    }

}
