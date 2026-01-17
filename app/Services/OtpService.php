<?php
namespace App\Services;
use Illuminate\Support\Facades\Redis;

class OtpService {


    public function generateOtp(String $email): String
    {
        $key = "otp:" . $email;
        $otp = sprintf("%06d", random_int(0, 999999));

        Redis::setex($key, 300, $otp);

        return $key;
    }

    public function verifyOtp(String $email, String $otp): Boolean
    {
        $key = "otp:" . $email;
        $value = Redis::get($key);

        if($value === null){
            return false;
        }

        return $value === $otp;
    }

    public function deleteOtp(String $email): void
    {
        $key = "otp:" . $email;
        Redis::del($key);
    }

}
