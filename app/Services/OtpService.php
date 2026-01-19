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

        // Return the actual OTP so it can be emailed to the user

        return $otp;
    }

    // Improved version:

    public function verifyOtp(string $email, string $otp, int $maxAttempts = 5, int $ttlSeconds = 300): int
    {
        $otpKey = "otp:" . $email;
        $attemptsKey = "otp_attempts:" . $email;

        // Lua script for atomic verification + attempt limiting
        $lua = <<<LUA
            local otpKey = KEYS[1]
            local attemptsKey = KEYS[2]

            local providedOtp = ARGV[1]
            local maxAttempts = tonumber(ARGV[2])
            local ttl = tonumber(ARGV[3])

            local storedOtp = redis.call("GET", otpKey)
            if not storedOtp then
                return 0
            end

            -- Increment attempts
            local attempts = redis.call("INCR", attemptsKey)
            if attempts == 1 then
                redis.call("EXPIRE", attemptsKey, ttl)
            end

            -- Too many attempts
            if attempts > maxAttempts then
                redis.call("DEL", otpKey)
                redis.call("DEL", attemptsKey)
                return -1
            end

            -- Correct OTP
            if storedOtp == providedOtp then
                redis.call("DEL", otpKey)
                redis.call("DEL", attemptsKey)
                return 1
            end

            -- Incorrect OTP
            return 0
        LUA;

        try {
            // Execute Lua script atomically
            $result = Redis::eval($lua, 2, $otpKey, $attemptsKey, $otp, $maxAttempts, $ttlSeconds);
            return (int) $result;
        } catch (\Throwable $e) {
            // Redis failure -> fallback to Cache verification
            return $this->verifyOtpWithCache($email, $otp, $maxAttempts, $ttlSeconds);
        }
    }

    private function verifyOtpWithCache(string $email, string $otp, int $maxAttempts = 5, int $ttlSeconds = 300): int
    {
        $otpKey = "otp:" . $email;
        $attemptsKey = "otp_attempts:" . $email;

        // Get stored OTP from cache
        $storedOtp = Cache::get($otpKey);
        if ($storedOtp === null) {
            return 0; // OTP not found
        }

        // Get attempts from cache
        $attempts = Cache::get($attemptsKey, 0);
        $attempts++;

        // Check max attempts
        if ($attempts > $maxAttempts) {
            Cache::forget($otpKey);
            Cache::forget($attemptsKey);
            return -1; // Too many attempts
        }

        // Store attempts with TTL
        Cache::put($attemptsKey, $attempts, now()->addSeconds($ttlSeconds));

        // Verify OTP
        if (hash_equals($storedOtp, $otp)) {
            // Success - delete OTP and attempts
            Cache::forget($otpKey);
            Cache::forget($attemptsKey);
            return 1; // Valid OTP
        }

        return 0; // Invalid OTP
    }
//    public function verifyOtp(string $email, string $otp): bool
//    {
//        $key = "otp:" . $email;
//
//        try {
//            $storedOtp = Redis::get($key);
//        } catch (\Exception $e) {
//            $storedOtp = Cache::get($key);
//        }
//
//        if ($storedOtp === null) {
//            return false;
//        }
//
//        // Constant-time comparison
//        if (!hash_equals($storedOtp, $otp)) {
//            return false;
//        }
//
//        // Invalidate OTP after successful use
//        try {
//            Redis::del($key);
//        } catch (\Exception $e) {
//            Cache::forget($key);
//        }
//
//        return true;
//    }

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
            // Send the email synchronously so it is dispatched immediately
            Mail::to($user->email)->send(new OtpMail($otp, $user));
        } catch (\Exception $e) {
            // Log the error but don't expose it to user
            Log::error('Failed to send OTP email: ' . $e->getMessage());
            throw new \Exception('Failed to send email. Please try again later.' . $e->getMessage());
        }
    }

}
