<?php


namespace App\Lib\FCM;


use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Facades\FCM;
use LaravelFCM\Sender\FCMSender;
use function GuzzleHttp\Psr7\str;

class PushNotification
{

    public function PushNotification($tokens, $title, $body)
    {
        $optionBuilder = new OptionsBuilder();
        $option = $optionBuilder->build();


        $notificationBuilder = new PayloadNotificationBuilder($title);
        $notificationBuilder->setBody($body)->setSound('default');
        $notification = $notificationBuilder->build();

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['type_id' => '2']);
        $dataBuilder->addData(['product_id' => '107']);
        $data = $dataBuilder->build();


        $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);

        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();
        $downstreamResponse->tokensToDelete();
        $downstreamResponse->tokensToModify();
        $downstreamResponse->tokensToRetry();
        $downstreamResponse->tokensWithError();

        return strval($downstreamResponse->numberFailure());
    }

}
