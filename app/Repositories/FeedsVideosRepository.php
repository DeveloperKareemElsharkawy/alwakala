<?php


namespace App\Repositories;


use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\FeedsVideos;

class FeedsVideosRepository
{

    public function uploadFeedsVideos($request)
    {

        $row = new FeedsVideos();
        $row->video = UploadImage::uploadVideoToStorage($request->video, 'feeds');;
        $row->store_id = $request->store_id;
        $row->verified = false;
        $row->save();

    }

    public function deleteVideo($id)
    {

        FeedsVideos::query()->where('id', $id)->delete();

    }

    public function changeVideoStatus($request)
    {
        $row = FeedsVideos::query()->where('id', $request->id)->first();
        $row->verified = $request->status;
        $row->save();
    }

    public function listAllVideosByStoreId($id)
    {
        $videos = FeedsVideos::query()->where('store_id', $id)->get();
        foreach ($videos as $video) {
            $video->video = config('filesystems.aws_base_url') . $video->video;
        }
        return $videos;
    }

    public function listAllVideos()
    {
        $videos = FeedsVideos::query()->select('feeds_videos.id', 'feeds_videos.video', 'feeds_videos.created_at', 'stores.name')
            ->join('stores', 'feeds_videos.store_id', '=', 'stores.id')
            ->get();
        foreach ($videos as $video) {
            $video->video = config('filesystems.aws_base_url') . $video->video;
        }
        return $videos;
    }

}
