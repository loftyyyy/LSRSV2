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
     * Remove an image
     */
    public function destroy(Inventory $inventory, InventoryImage $image): JsonResponse
    {
        // Ensure the image belongs to the inventory item
        if ($image->item_id !== $inventory->item_id) {
            return response()->json([
                'message' => 'Image not found for this inventory item'
            ], 404);
        }

        $wasPrimary = $image->is_primary;

        // Delete the physical file
        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        // Delete the database record
        $image->delete();

        // If deleted image was primary, set another image as primary
        if ($wasPrimary) {
            $nextImage = $inventory->images()
                ->orderBy('display_order', 'asc')
                ->first();

            if ($nextImage) {
                $nextImage->update(['is_primary' => true]);
            }
        }

        return response()->json([
            'message' => 'Image deleted successfully'
        ]);
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

    /**
     * Reorder images
     */
    public function reorder(Request $request, Inventory $inventory): JsonResponse
    {
        $request->validate([
            'image_orders' => 'required|array',
            'image_orders.*.image_id' => 'required|exists:inventory_images,image_id',
            'image_orders.*.display_order' => 'required|integer|min:1'
        ]);

        foreach ($request->image_orders as $orderData) {
            $image = InventoryImage::find($orderData['image_id']);

            // Ensure the image belongs to this inventory item
            if ($image && $image->item_id === $inventory->item_id) {
                $image->update(['display_order' => $orderData['display_order']]);
            }
        }

        $updatedImages = $inventory->images()
            ->orderBy('display_order', 'asc')
            ->get();

        return response()->json([
            'message' => 'Images reordered successfully',
            'data' => $updatedImages
        ]);
    }

    /**
     * Delete all images for an inventory item
     */
    public function destroyAll(Inventory $inventory): JsonResponse
    {
        $images = $inventory->images;

        foreach ($images as $image) {
            // Delete physical file
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
        }

        // Delete all image records
        $inventory->images()->delete();

        // Optionally delete the entire folder
        $folderPath = 'inventory/' . $inventory->item_id;
        if (Storage::disk('public')->exists($folderPath)) {
            Storage::disk('public')->deleteDirectory($folderPath);
        }

        return response()->json([
            'message' => 'All images deleted successfully'
        ]);
    }

    /**
     * Get primary/main image for an inventory item
     */
    public function getPrimary(Inventory $inventory): JsonResponse
    {
        $primaryImage = $inventory->images()
            ->where('is_primary', true)
            ->first();

        if (!$primaryImage) {
            // If no primary image set, get the first one
            $primaryImage = $inventory->images()
                ->orderBy('display_order', 'asc')
                ->first();
        }

        if (!$primaryImage) {
            return response()->json([
                'message' => 'No images found for this inventory item',
                'data' => null
            ]);
        }

        return response()->json([
            'data' => $primaryImage
        ]);
    }
     /**
     * Get images by view type (front, back, side, etc.)
     */
    public function getByViewType(Inventory $inventory, string $viewType): JsonResponse
    {
        $validViewTypes = ['front', 'back', 'side', 'detail', 'full'];

        if (!in_array($viewType, $validViewTypes)) {
            return response()->json([
                'message' => 'Invalid view type. Must be one of: ' . implode(', ', $validViewTypes)
            ], 400);
        }

        $images = $inventory->images()
            ->where('view_type', $viewType)
            ->orderBy('display_order', 'asc')
            ->get();

        return response()->json([
            'data' => $images
        ]);
    }

    /**
     * Replace an existing image
     */
    public function replace(Request $request, Inventory $inventory, InventoryImage $image): JsonResponse
    {
        // Ensure the image belongs to the inventory item
        if ($image->item_id !== $inventory->item_id) {
            return response()->json([
                'message' => 'Image not found for this inventory item'
            ], 404);
        }

        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120'
        ]);

        // Delete old image file
        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        // Store new image
        $newImage = $request->file('image');
        $filename = Str::uuid() . '.' . $newImage->getClientOriginalExtension();
        $path = $newImage->storeAs('inventory/' . $inventory->item_id, $filename, 'public');

        // Update image record
        $image->update([
            'image_path' => $path,
            'image_url' => Storage::url($path),
            'file_size' => $newImage->getSize(),
            'mime_type' => $newImage->getMimeType()
        ]);

        return response()->json([
            'message' => 'Image replaced successfully',
            'data' => $image
        ]);
    }
    /**
     * Bulk delete multiple images
     */
    public function bulkDestroy(Request $request, Inventory $inventory): JsonResponse
    {
        $request->validate([
            'image_ids' => 'required|array',
            'image_ids.*' => 'required|exists:inventory_images,image_id'
        ]);

        $deletedCount = 0;
        $hadPrimary = false;

        foreach ($request->image_ids as $imageId) {
            $image = InventoryImage::find($imageId);

            // Ensure the image belongs to this inventory item
            if ($image && $image->item_id === $inventory->item_id) {
                if ($image->is_primary) {
                    $hadPrimary = true;
                }

                // Delete physical file
                if (Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                }

                $image->delete();
                $deletedCount++;
            }
        }

        // If a primary image was deleted, set a new one
        if ($hadPrimary) {
            $nextImage = $inventory->images()
                ->orderBy('display_order', 'asc')
                ->first();

            if ($nextImage) {
                $nextImage->update(['is_primary' => true]);
            }
        }

        return response()->json([
            'message' => "{$deletedCount} image(s) deleted successfully",
            'deleted_count' => $deletedCount
        ]);
    }
      /**
     * Get inventory items with their primary images
     */
    public function getInventoryWithImages(Request $request): JsonResponse
    {
        $query = Inventory::with(['images' => function ($q) {
            $q->where('is_primary', true)
              ->orWhereIn('image_id', function ($subQ) {
                  $subQ->selectRaw('MIN(image_id)')
                       ->from('inventory_images')
                       ->groupBy('item_id');
              });
        }]);

        // Apply filters if provided
        if ($request->has('item_type')) {
            $query->where('item_type', $request->get('item_type'));
        }

        $perPage = $request->get('per_page', 15);
        $inventories = $query->paginate($perPage);

        return response()->json($inventories);
    }
}
