<?php

namespace App\Http\Controllers\Seller;


use App\Http\Controllers\BaseController;
use App\Http\Requests\SellerApp\QRCode\QRCodeRequest;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Helpers\Product\ProductHelper;
use App\Lib\Helpers\UserId\UserId;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\Store;
use App\Models\User;
use App\Repositories\ProfileRepository;
use App\Repositories\StoreRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Image;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeController extends BaseController
{

    /**
     * @var mixed|string
     */
    private $lang;
    public $profileRepo;

    public function __construct(Request $request, ProfileRepository $profileRepository)
    {
        $this->lang = LangHelper::getDefaultLang($request);
        $this->profileRepo = $profileRepository;

    }


    public function getStoreByQRCode(QRCodeRequest $request)
    {
        try {
            $userId = UserId::UserId($request) ?? 0;

            $qrCode = request()->get('qr_code');

            $store = Store::query()->where('qr_code', $qrCode)->first();

            $storeProfile = $this->profileRepo->getStoreProfileForVisitors($userId, $this->lang, $store->id);

            return $this->success(['message' => 'Store Profile', 'data' => $storeProfile]);

        } catch (\Exception $e) {
            return $e->getMessage();
            Log::error('error in listing Store By QR Code' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function myStoreQRCode(Request $request)
    {
        try {
            $userId = UserId::UserId($request);

            $store = StoreRepository::getStoreByUserId($userId);

            return $this->success([
                'message' => 'Store Profile',
                'data' => config('filesystems.aws_base_url') . $store->qr_code_image
            ]);

        } catch (\Exception $e) {
            return $e->getMessage();
            Log::error('error in listing Store By QR Code' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
