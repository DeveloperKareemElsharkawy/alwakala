<?php

namespace App\Http\Controllers\Development;

use App\Lib\Services\ImageUploader\UploadImage;
use App\Mail\Auth\PasswordRequestReset;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;

class ImagesController extends Controller
{
    public function uploadImage(Request $request)
    {
        return UploadImage::uploadImageToStorageV2($request->file('image'), 'dev');
    }

    public function getData()
    {
        echo phpinfo();
        $upload_max_size = ini_get('upload_max_filesize');
        $post_max_size = ini_get('post_max_size');
        $memory_limit = ini_get('memory_limit');
        return array(
            'upload_max_filesize' => $upload_max_size,
            'post_max_size' => $post_max_size,
            'memory_limit' => $memory_limit,
        );
    }

    public function testImageFromMobile(Request $request)
    {
        try {
            return config('filesystems.aws_base_url') . UploadImage::uploadImageToStorage($request->file('image'), 'dev');
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }

    public function lang()
    {
        $data = [
            'message' => trans('messages.messages.hello')
        ];
        return response()->json([
            $data
        ]);
    }

    public function sendEmail()
    {
        $user = \App\Models\User::query()->where('id', 23)->first();
        Mail::to('reciver@gmail.com')->send(new PasswordRequestReset($user));
    }
}
