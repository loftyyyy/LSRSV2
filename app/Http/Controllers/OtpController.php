<?php

namespace App\Http\Controllers\OTPController;

use App\Http\Controllers\Controller;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OtpController extends Controller
{
    public function generateOtp(Request $request): JsonResponse
    {
        $credential  = $request->validate([
            'email' => ['required', 'email'],
        ]);


    }


}
