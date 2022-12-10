<?php


namespace App\Lib\Services\ImageUploader;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class UploadImage
{
    public static function uploadImageToStorage($file, $directory)
    {
        try {
            $extension = $file->getClientOriginalExtension();
            $file_name = time() . rand(10, 99) . '.' . $extension;
            $path = '/images/' . $directory . '/' . $file_name;
            Storage::disk('s3')->put($path, file_get_contents($file));
            return $path;
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }

    public static function uploadSVGToStorage($file)
    {
        try {
            $output_file = '/images/qr-code/stores/img-' . time() . '.png';
            Storage::disk('s3')->put($output_file, $file);
            return $output_file;
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }

    public static function uploadVideoToStorage($file, $directory)
    {
        try {
            $extension = $file->getClientOriginalExtension();
            $file_name = time() . rand(10, 99) . '.' . $extension;
            $path = '/videos/' . $directory . '/' . $file_name;
            Storage::disk('s3')->put($path, file_get_contents($file));
            return $path;
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }

    public static function uploadRecordToStorage($file, $directory)
    {
        try {
            $extension = $file->getClientOriginalExtension();
            $file_name = time() . rand(10, 99) . '.' . $extension;
            $path = '/records/' . $directory . '/' . $file_name;
            Storage::disk('s3')->put($path, file_get_contents($file));
            return $path;
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }

    public static function uploadImageToStorageOld($file, $directory, $dimensions = [308, 452])
    {
        try {
            $extension = $file->getClientOriginalExtension();
            $file_name = time() . rand(10, 99) . '.' . $extension;
            $path = '/images/' . $directory . '/' . $file_name;
            $image = Image::make($file->getRealPath())
                ->resize($dimensions[0], $dimensions[1]);
            $image = $image->encode();
            Storage::disk('s3')->put($path, $image->__toString());
            return $path;
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}
