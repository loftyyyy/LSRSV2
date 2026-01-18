<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OtpController extends Controller
{

    public function generateOtp(Request $request): JsonResponse
    {
        try {
            // Validate email input
            $validated = $request->validate([
                'email' => ['required', 'email'],
            ]);

            // Check if user exists
            $user = User::where('email', $validated['email'])->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'errors' => [
                        'email' => ['The provided credentials do not match our records.']
                    ]
                ], 422); // 422 Unprocessable Entity for validation errors
            }

            // Generate OTP and send email
            $otpService = new OtpService();
            $otp = $otpService->generateOtp($validated['email']);
            $otpService->sendEmail($otp, $user);

            return response()->json([
                'success' => true,
                'message' => 'Password reset OTP has been sent to your email.'
            ], 200);

        } catch (ValidationException $e) {
            // Return validation errors as JSON
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);

        } catch (\Throwable $e) {
            // Return general error as JSON
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function resendOtp(Request $request): JsonResponse
    {


    }
    public function verifyOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'otp' => ['required'],
        ]);

    }


}
