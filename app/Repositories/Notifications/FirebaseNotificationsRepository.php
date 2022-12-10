<?php

namespace App\Repositories\Notifications;

use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use Twilio\Rest\Client;

class FirebaseNotificationsRepository
{

    function SendNotification($tokens, $title, $body, $data)
    {
        try {
            $optionBuilder = new OptionsBuilder();
            $option = $optionBuilder->build();

            $notificationBuilder = new PayloadNotificationBuilder($title);
            $notificationBuilder->setBody($body)->setSound('default');
            $notification = $notificationBuilder->build();

            $dataBuilder = new PayloadDataBuilder();
            foreach ($data as $key => $value) {
                $dataBuilder->addData([$key => $value]);
            }
            $data = $dataBuilder->build();
            $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
            $downstreamResponse->numberSuccess();
            $downstreamResponse->numberFailure();
            $downstreamResponse->numberModification();
            $downstreamResponse->tokensToDelete();
            $downstreamResponse->tokensToModify();
            $downstreamResponse->tokensToRetry();
            $downstreamResponse->tokensWithError();
            return ($downstreamResponse->numberSuccess());
        } catch (\Exception $e) {
            return false;
        }
    }
}
