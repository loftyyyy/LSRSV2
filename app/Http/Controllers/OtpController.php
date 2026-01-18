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

    public function generateOtp(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'email' => ['required', 'email'],
            ]);

            $user = User::where('email', $validated['email'])->first();

            if (!$user) {
                return back()->withErrors([
                    'email' => 'The provided credentials do not match our records.'
                ]);
            }

            $otpService = new OtpService();
            $otp = $otpService->generateOtp($validated['email']);
            $otpService->sendEmail($otp, $user);

            return back()->with('status', 'Password reset OTP has been sent to your email.');

        } catch (ValidationException $e) {

            return back()->withErrors($e->errors());

        } catch (\Throwable $e) {

            return back()->with('error', 'Something went wrong. Please try again later. ' . $e->getMessage());
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
