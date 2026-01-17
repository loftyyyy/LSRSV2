<?php

namespace App\Http\Controllers\OTPController;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OtpController extends Controller
{
    public function generateOtp(Request $request): JsonResponse
    {
        try{
            $request->validate([
                'email' => ['required', 'email'],
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return resonse()->json([
                    'email' => 'The provided credentials do not match our records.',
                ]);
            }



            return response()->json([
                'message' => 'OTP generated successfully',
            ]);

        }catch (\Exception $exception){
            return response()->json(['message' => $exception->getMessage()]);
        }
    }


}
