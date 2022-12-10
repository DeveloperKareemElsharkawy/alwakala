<?php

namespace App\Listeners\Store;

use App\Enums\Views\AViews;
use App\Events\Store\VisitStore;
use App\Lib\FCM\PushNotification;
use App\Lib\Helpers\Views\ViewsHelper;
use App\Models\UserDeviceToken;
use App\Repositories\NotificationRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class FavoriteStore
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
    public function handle(\App\Events\Store\FavoriteStore  $event)
    {
        $data = [];
        $data['title'] = 'elwekala';
        $data['body'] = 'favorite_store';
        $data['item_id'] = $event->itemId;
        $data['image'] = null;
        $data['item_type']=NotificationRepository::getNotificationTypeId('store');
        foreach ($event->usersId as $user_id) {
            $data['user_id'] = $user_id;
            NotificationRepository::Save($data);
        }

//        $usersDeviceToken = UserDeviceToken::query()->whereIn('user_id', $event->usersId)->pluck('token')->toArray();
//
//        $pushNotificationObject = new PushNotification();
//
//        $pushNotificationObject->PushNotification($usersDeviceToken, 'elwekala', trans('messages.notifications.favorite_store'));

    }
}
