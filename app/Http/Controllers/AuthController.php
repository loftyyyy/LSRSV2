<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

abstract class AuthController extends Controller
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
