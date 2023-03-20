<?php

namespace App\Lib\Helpers\Feed;

use App\Lib\Helpers\UserId\UserId;
use App\Models\Feed;
use App\Models\SellerFavorite;
use App\Models\User;
use App\Models\View;

class FeedHelper
{

    /**
     * @param $youtubeUrl
     * @return null|string
     */
    public static function getYouTubeVideoId($youtubeUrl): null|string
    {
        if (!$youtubeUrl)
            return null;

        $parts = parse_url($youtubeUrl);
        if(isset($parts['query'])){
            parse_str($parts['query'], $qs);
            if(isset($qs['v'])){
                return $qs['v'];
            }else if(isset($qs['vi'])){
                return $qs['vi'];
            }
        }
        if(isset($parts['path'])){
            $path = explode('/', trim($parts['path'], '/'));
            return $path[count($path)-1];
        }
        return '';
    }


    /**
     * @param $youtubeUrl
     * @return string|null
     */
    public static function getYouTubeVideoThumbURL($youtubeUrl): null|string
    {
        if (!$youtubeUrl)
            return null;

        $youtubeID = static::getYouTubeVideoId($youtubeUrl);
        return  'https://img.youtube.com/vi/' . $youtubeID . '/mqdefault.jpg';
    }


}
