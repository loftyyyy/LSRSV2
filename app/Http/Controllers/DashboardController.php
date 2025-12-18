<?php

use Illuminate\Routing\Controller;

class DashboardController extends Controller
{

    /**
     * Display Dashboard Page
     */
    public function showDashboardPage()
    {
        return view('dashboard');
    }
}
