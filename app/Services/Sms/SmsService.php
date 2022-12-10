<?php

namespace App\Services\Sms;

use App\Repositories\Sms\SmsTwilioRepository;

class SmsService
{
    private $smsRepository;
    private $default_lang = 'ar';

    public function __construct(SmsTwilioRepository $smsRepository)
    {
        $this->smsRepository = $smsRepository;
    }

    public function sendSms($phone, $message)
    {
        return $this->smsRepository->SendSmsMobile($phone, $message);
    }

    public function sendSmsToStoreRegister($phone, $code, $lang)
    {
        if (!$lang)
            $lang = $this->default_lang;
        $message = trans('messages.sms.store_register', ['code' => $code], $lang);
        return $this->sendSms($phone, $message);
    }

    public function sendSmsToStoreForgetPassword($phone, $code, $lang = "")
    {
        if (!$lang)
            $lang = $this->default_lang;
        $message = trans('messages.sms.store_forget_password', ['code' => $code], $lang);
        return $this->sendSms($phone, $message);
    }

    public function sendSmsToStoreChangeCredentials($phone, $code, $lang = "")
    {
        if (!$lang)
            $lang = $this->default_lang;
        $message = trans('messages.sms.store_change_credential_mobile', ['code' => $code], $lang);
        return $this->sendSms($phone, $message);
    }
}
