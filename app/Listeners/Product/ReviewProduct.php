<?php

namespace App\Listeners\Product;


use App\Events\Product\VisitProduct;
use App\Lib\FCM\PushNotification;

use App\Models\UserDeviceToken;
use App\Repositories\NotificationRepository;

class ReviewProduct
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
     * @param \App\Events\Product\ReviewProduct $event
     * @return void
     */
    public function handle(\App\Events\Product\ReviewProduct $event)
    {
        $data = [];
        $data['title'] = 'elwekala';
        $data['body'] = 'review_product';
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
//        $pushNotificationObject->PushNotification($usersDeviceToken, 'elwekala', trans('messages.notifications.review_product'));
    }
}
