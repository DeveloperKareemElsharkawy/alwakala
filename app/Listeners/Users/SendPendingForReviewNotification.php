<?php

namespace App\Listeners\Users;

use App\Events\Users\PendingForReview;
use App\Lib\FCM\PushNotification;
use App\Models\UserDeviceToken;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPendingForReviewNotification
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
     * @param \App\Events\Users\PendingForReview $event
     * @return void
     */
    public function handle(\App\Events\Users\PendingForReview $event)
    {

        $data = [];
        $data['title'] = 'elwekala';
        $data['body'] = 'store_is_pending';
        $data['item_id'] = null;
        $data['image'] = null;
        $data['item_type'] = NotificationRepository::getNotificationTypeId('approve-store');

        foreach ($event->usersId as $userId) {
            $data['user_id'] = $userId;
            NotificationRepository::Save($data);
        }

        $usersDeviceToken = UserDeviceToken::query()->whereIn('user_id', $event->usersId)->pluck('token')->toArray();

        if (count($usersDeviceToken)) {
            $pushNotificationObject = new PushNotification();
            $pushNotificationObject->PushNotification($usersDeviceToken, 'elwekala', trans('messages.auth.pending_for_review'));
        }

    }
}
