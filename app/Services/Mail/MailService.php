<?php

namespace App\Services\Mail;

use App\Jobs\Emails\SendEmailJob;
use App\Repositories\Mail\MailRepository;
use Carbon\Carbon;

class MailService
{
    private $default_lang = 'ar';

    private $mailRepository;

    public function __construct(MailRepository $mailRepository)
    {
        $this->mailRepository = $mailRepository;
    }

    public function sendMailViaJOb($email, $data)
    {
        
        return $this->mailRepository->SendMail($email, $data);
    }

    public function sendMailToStoreRegister($email, $user_name, $code, $lang)
    {
        if (!$lang)
            $lang = $this->default_lang;
        $data = [
            'user_name' => $user_name,
            'reset_code' => $code,
            'subject' => "Register Activation Code",
            'message' => trans('messages.sms.store_register', ['code' => $code], $lang)
        ];
        $this->sendMailViaJOb($email, $data);
    }
}
