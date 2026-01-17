<?php
namespace App\Services;
class OtpService {


    public function generateOtp(String $email)
    {
        $key = "otp:" . $email;
        $otp = sprintf("%06d", random_int(0, 999999));
        


    }
}
