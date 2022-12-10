<?php

namespace App\Listeners\Users;

use App\Events\Order\ApproveOrder;
use App\Lib\FCM\PushNotification;
use App\Models\UserDeviceToken;
use App\Repositories\NotificationRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ApprovePendingSeller
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
     * @param \App\Events\Users\ApprovePendingSeller $event
     * @return void
     */
    public function handle(\App\Events\Users\ApprovePendingSeller $event)
    {
        $data = [];
        $data['title'] = 'elwekala';
        $data['body'] = 'approve_seller';
        $data['item_id'] = null;
        $data['image'] = null;
        $data['item_type'] = NotificationRepository::getNotificationTypeId('approve-seller');
        foreach ($event->usersId as $user_id) {
            $data['user_id'] = $user_id;
            NotificationRepository::Save($data);
        }

        $usersDeviceToken = UserDeviceToken::query()->whereIn('user_id', $event->usersId)->pluck('token')->toArray();

        if (count($usersDeviceToken)) {
            $pushNotificationObject = new PushNotification();
            $pushNotificationObject->PushNotification($usersDeviceToken, 'elwekala', trans('messages.notifications.approve_seller'));
        }


    }
}
