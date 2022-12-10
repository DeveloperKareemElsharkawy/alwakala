<?php


namespace App\Lib\S3;


use Illuminate\Support\Facades\Storage;

class S3StorageHandler
{
    public function generateS3ImagePreSignedUrl(string $path, int $expiryInMinutes = 30) : string {
        if (!$path)
            return "";

        $bucket = config("filesystems.IMAGES_BUCKET");
        if($path && $path[0] == "/") {
            $path = substr($path, 1);
        }

        $s3 = Storage::disk('s3');
        $client = $s3->getDriver()->getAdapter()->getClient();
        $expiry = "+$expiryInMinutes minutes";

        $command = $client->getCommand('GetObject', [
            'Bucket' => $bucket,
            'Key'    => $path
        ]);

        $request = $client->createPresignedRequest($command, $expiry);

        return (string) $request->getUri();
    }
}
