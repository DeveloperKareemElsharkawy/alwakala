<?php

namespace App\Listeners\Order;

use App\Events\Order\ApproveOrder;
use App\Events\Order\ShippingOrder;
use App\Lib\FCM\PushNotification;
use App\Models\UserDeviceToken;
use App\Repositories\NotificationRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ShippingOrderNotification
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
     * @param  ShippingOrder  $event
     * @return void
     */
    public function handle(ShippingOrder $event)
    {
        $data = [];
        $data['title'] = 'elwekala';
        $data['body'] = 'shipping_order';
        $data['item_id'] = $event->itemId;
        $data['image'] = null;
        $data['item_type']=NotificationRepository::getNotificationTypeId('order');
        foreach ($event->usersId as $user_id) {

            $data['user_id'] = $user_id;
            NotificationRepository::Save($data);
        }

//        $usersDeviceToken = UserDeviceToken::query()->whereIn('user_id', $event->usersId)->pluck('token')->toArray();
//
//        $pushNotificationObject = new PushNotification();
//
//        $pushNotificationObject->PushNotification($usersDeviceToken, 'elwekala', trans('messages.notifications.approve_order'));


    }
}
