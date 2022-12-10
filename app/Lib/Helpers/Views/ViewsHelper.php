<?php

namespace App\Lib\Helpers\Views;

use App\Lib\Helpers\UserId\UserId;
use App\Models\View;

class ViewsHelper
{

    public static function addView($request, $type, $userId, $itemId)
    {

        $view = new View;
        $view->user_id = $userId;
        $view->item_id = $itemId;
        $view->item_type = $type;
        $view->ip = $_SERVER['REMOTE_ADDR'];
        $view->browser = $request->header('User-Agent');
        
        $view->save();
    }

    public static function getViewsCount($itemId, $ratedType)
    {
        $modelName = strtoupper(class_basename($ratedType));

        return View::query()->where([['item_type', $modelName], ['item_id', $itemId]])->distinct('ip')->count();
    }

}
