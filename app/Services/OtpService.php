<?php
namespace App\Services;
use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class OtpService {


    public function generateOtp(String $email): String
    {
        $key = "otp:" . $email;
        $otp = sprintf("%06d", random_int(0, 999999));

        // Try Redis first, fallback to Cache if Redis is not available
        try {
            Redis::setex($key, 300, $otp);
        } catch (\Exception $e) {
            // Fallback to file cache if Redis fails
            Cache::put($key, $otp, now()->addMinutes(5));
        }

        return $key;
    }

    public function verifyOtp(String $email, String $otp): bool
    {
        $key = "otp:" . $email;
        $value = null;

        // Try Redis first, fallback to Cache
        try {
            $value = Redis::get($key);
        } catch (\Exception $e) {
            $value = Cache::get($key);
        }

        if($value === null){
            return false;
        }

        return $value === $otp;
    }

    public function deleteOtp(String $email): void
    {
        $key = "otp:" . $email;

        // Try Redis first, fallback to Cache
        try {
            Redis::del($key);
        } catch (\Exception $e) {
            Cache::forget($key);
        }
    }

    public function sendEmail(String $otp, User $user): void
    {
        try {
            Mail::to($user->email)->queue(new OtpMail($otp, $user));
        } catch (\Exception $e) {
            // Log the error but don't expose it to user
            Log::error('Failed to send OTP email: ' . $e->getMessage());
            throw new \Exception('Failed to send email. Please try again later.' . $e->getMessage());
        }
    }

}
