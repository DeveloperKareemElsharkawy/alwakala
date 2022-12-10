<?php

namespace App\Services\Notifications;

use App\Repositories\Notifications\FirebaseNotificationsRepository;

class NotificationsService
{
    private $firebaseNotificationsRepository;
    private $default_lang = 'ar';

    public function __construct(FirebaseNotificationsRepository $firebaseNotificationsRepository)
    {
        $this->firebaseNotificationsRepository = $firebaseNotificationsRepository;
    }

    public function sendFirebaseNotifications($tokens, $title, $body, $data)
    {
        return $this->firebaseNotificationsRepository->SendNotification($tokens, $title, $body, $data);
    }

    public function sendnotificationsToStoreActivationAccount($store, $lang = "")
    {
        if (!$lang)
            $lang = $this->default_lang;
        if ($store->user->device_token && $store->user->device_token->token) {
            $message = trans('messages.notifications.store_account_confirmed', [], $lang);
            return $this->sendFirebaseNotifications($store->user->device_token->token, "Elwekala", $message, [
                "type_id" => 1
            ]);
        }
        return 0;
    }
}
