<?php

namespace App\Repositories\Sms;

use Twilio\Rest\Client;

class SmsTwilioRepository
{

    //
    private $account_sid = 'ACa28464cbcc676c63fd5de169e2641377';
    private $auth_token = '89060144828272a9c45429f315d0b104';
    private $twilio_number = '+19804003811';
    //souq
    // private $account_sid = 'AC3101a99f30c4d5c9860b7a4a00aecefd';
    // private $auth_token = '92d0ccf3a6b8d818c92268b2d807cfde';
    // private $twilio_number = '+13233100736';
    // private $account_sid = 'ACd69a467b66033e1305938a0dc7f3a429';
    // private $auth_token = 'd21301c38c7a52e07ef58b5b0d84cc0a';
    // private $twilio_number = '+12033507815';
    private $twilio;

    public function __construct()
    {
        $this->twilio = new Client($this->account_sid, $this->auth_token);
    }

    function SendSmsMobile($phone, $message)
    {
        try {
            $phone = $this->add_country_key($phone);
            $res = $this->twilio->messages->create($phone, ['from' => $this->twilio_number, 'body' => $message]);
            // return $this->twilio->messages->create($phone, ['messagingServiceSid' => 'MG6a05c55d0cfd754b7c45f9df5d4774bb', 'body' => $message]);
            return ($res);
        } catch (\Exception $e) {
            return false;
        }
    }

    private function add_country_key($phone)
    {
        return  "+2" . $phone;
    }
}
