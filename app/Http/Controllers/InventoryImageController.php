<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryImage;
use App\Http\Requests\StoreInventoryImageRequest;
use App\Http\Requests\UpdateInventoryImageRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryImageController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Inventory  $inventory)
    {
        $images = $inventory->images()
            ->orderBy('is_primary', 'desc')
            ->orderBy('display_order', 'asc')
            ->get();

        return response()->json([
            'data' => $images
        ]);
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
    public function store(Request $request, Inventory  $inventory): JsonResponse
    {
        $request->validate([
            'images' => 'required|array|max:10',
            'images.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120', // 5MB max
            'view_types' => 'nullable|array',
            'view_types.*' => 'nullable|string|in:front,back,side,detail,full'
        ]);

        $uploadedImages = [];
        $existingImagesCount = $inventory->images()->count();

        // Check if this is the first image to set as primary
        $shouldSetPrimary = $existingImagesCount === 0;

        foreach ($request->file('images') as $index => $image) {
            // Generate unique filename
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();

            // Store image in public disk under inventory folder
            $path = $image->storeAs('inventory/' . $inventory->item_id, $filename, 'public');

            // Get view type if provided
            $viewType = $request->view_types[$index] ?? null;

            // Create image record
            $inventoryImage = $inventory->images()->create([
                'image_path' => $path,
                'image_url' => Storage::url($path),
                'view_type' => $viewType,
                'is_primary' => $shouldSetPrimary && $index === 0,
                'display_order' => $existingImagesCount + $index + 1,
                'file_size' => $image->getSize(),
                'mime_type' => $image->getMimeType()
            ]);

            $uploadedImages[] = $inventoryImage;
        }

        return response()->json([
            'message' => count($uploadedImages) . ' image(s) uploaded successfully',
            'data' => $uploadedImages
        ], 201);
    }

    /**
     * Display a specific image
     */
    public function show(Inventory $inventory, InventoryImage $image): JsonResponse
    {
        // Ensure the image belongs to the inventory item
        if ($image->item_id !== $inventory->item_id) {
            return response()->json([
                'message' => 'Image not found for this inventory item'
            ], 404);
        }

        return response()->json([
            'data' => $image
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InventoryImage $inventoryImage)
    {
        //
    }

    /**
     * Update image details (view type, display order, etc.)
     */
    public function update(Request $request, Inventory $inventory, InventoryImage $image): JsonResponse
    {
        // Ensure the image belongs to the inventory item
        if ($image->item_id !== $inventory->item_id) {
            return response()->json([
                'message' => 'Image not found for this inventory item'
            ], 404);
        }

        $request->validate([
            'view_type' => 'nullable|string|in:front,back,side,detail,full',
            'display_order' => 'nullable|integer|min:1',
            'caption' => 'nullable|string|max:255'
        ]);

        $image->update($request->only(['view_type', 'display_order', 'caption']));

        return response()->json([
            'message' => 'Image updated successfully',
            'data' => $image
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InventoryImage $inventoryImage)
    {
        //
    }
    /**
     * Set an image as primary/main image
     */
    public function setPrimary(Inventory $inventory, InventoryImage $image): JsonResponse
    {
        // Ensure the image belongs to the inventory item
        if ($image->item_id !== $inventory->item_id) {
            return response()->json([
                'message' => 'Image not found for this inventory item'
            ], 404);
        }

        // Remove primary status from all images of this inventory item
        $inventory->images()->update(['is_primary' => false]);

        // Set this image as primary
        $image->update(['is_primary' => true]);

        return response()->json([
            'message' => 'Primary image updated successfully',
            'data' => $image
        ]);
    }
}
