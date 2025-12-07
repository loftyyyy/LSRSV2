<?php

namespace App\Http\Controllers;

use App\Models\CustomerStatus;
use App\Http\Requests\StoreCustomerStatusRequest;
use App\Http\Requests\UpdateCustomerStatusRequest;

class CustomerStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerStatusRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CustomerStatus $customerStatus)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomerStatus $customerStatus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerStatusRequest $request, CustomerStatus $customerStatus)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomerStatus $customerStatus)
    {
        //
    }
}
