<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use Illuminate\Http\Request;

abstract class AuthController
{
    //
    public function login(Request $request)
    {

    }

    public function logout(Request $request)
    {
        auth()->logout();
        return redirect()->route('landing');
    }

    public function register(StoreCustomerRequest $request)
    {

    }

}
