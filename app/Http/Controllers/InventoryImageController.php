<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryImage;
use App\Http\Requests\StoreInventoryImageRequest;
use App\Http\Requests\UpdateInventoryImageRequest;
use Illuminate\Http\Request;

class InventoryImageController
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
    public function store(Request $request)
    {
        $request->validate([
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $inventory = Inventory::create($request->only([
            'item_type', 'sku', 'name', 'size', 'color', 'design', 'rental_price', 'status_id'
        ]));

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('inventory', 'public');

                $inventory->images()->create([
                    'image_path' => $path,
                    'is_primary' => $index === 0 // first image = main image
                ]);
            }
        }

        return redirect()->back()->with('success', 'Item created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(InventoryImage $inventoryImage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InventoryImage $inventoryImage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInventoryImageRequest $request, InventoryImage $inventoryImage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InventoryImage $inventoryImage)
    {
        //
    }
}
