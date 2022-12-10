<?php

namespace App\Listeners\Product;

use App\Enums\Views\AViews;
use App\Events\Store\VisitStore;
use App\Lib\FCM\PushNotification;
use App\Lib\Helpers\Views\ViewsHelper;
use App\Models\UserDeviceToken;
use App\Repositories\NotificationRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class FavoriteProduct
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param VisitStore $event
     * @return void
     */
    public function handle(\App\Events\Product\FavoriteProduct $event)
    {
        $data = [];
        $data['title'] = 'elwekala';
        $data['body'] = 'favorite_product';
        $data['item_id'] = $event->itemId;
        $data['image'] = $event->image;
        $data['item_type']=NotificationRepository::getNotificationTypeId('product');
        foreach ($event->usersId as $user_id) {
            $data['user_id'] = $user_id;
            NotificationRepository::Save($data);
        }

//        $usersDeviceToken = UserDeviceToken::query()->whereIn('user_id', $event->usersId)->pluck('token')->toArray();
//
//        $pushNotificationObject = new PushNotification();
//
//        $pushNotificationObject->PushNotification($usersDeviceToken, 'elwekala', trans('messages.notifications.favorite_product'));

    }
}
