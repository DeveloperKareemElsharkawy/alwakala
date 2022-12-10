<?php

namespace App\Repositories\Mail;

use App\Jobs\Emails\SendEmailJob;
use Carbon\Carbon;

class MailRepository
{

    function SendMail($email, $data)
    {
        try {
            $job = (new SendEmailJob($data, $email))->delay(Carbon::now()->addSeconds(1));
            dispatch($job);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
