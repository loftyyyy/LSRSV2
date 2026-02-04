<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryImage;
use App\Http\Requests\StoreInventoryImageRequest;
use App\Http\Requests\UpdateInventoryImageRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class InventoryImageController
{

    /**
     * Display a listing of the resource.
     */
    public function index(Inventory $inventory): JsonResponse
    {
        $images = $inventory->images()
            ->orderBy('is_primary', 'desc')
            ->orderBy('display_order', 'asc')
            ->get();

        $groupedByView = $images->groupBy('view_type');

        return response()->json([
            'data' => $images,
            'grouped_by_view' => $groupedByView,
            'summary' => [
                'total' => $images->count(),
                'primary' => $images->where('is_primary', true)->first(),
                'by_view_type' => [
                    'front' => $groupedByView->get('front', collect())->count(),
                    'back' => $groupedByView->get('back', collect())->count(),
                    'side' => $groupedByView->get('side', collect())->count(),
                    'detail' => $groupedByView->get('detail', collect())->count(),
                    'full' => $groupedByView->get('full', collect())->count(),
                ],
                'missing_required_views' => $this->getMissingRequiredViews($inventory)
            ]
        ]);
    }

    /**
     * Store newly created resources in storage.
     */
    public function store(Request $request, Inventory $inventory): JsonResponse
    {
        $request->validate([
            'images' => 'required|array|max:10',
            'images.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120', // 5MB max
            'view_types' => 'required|array',
            'view_types.*' => 'required|string|in:front,back,side,detail,full',
            'captions' => 'nullable|array',
            'captions.*' => 'nullable|string|max:255',
            'is_primary' => 'nullable|array',
            'is_primary.*' => 'nullable|string|in:0,1'
        ]);

        // Validate that images and view_types have the same count
        if (count($request->file('images')) !== count($request->view_types)) {
            return response()->json([
                'message' => 'Each image must have a corresponding view type'
            ], 422);
        }

        // Check for duplicate view types in the request
        $requestViewTypes = $request->view_types;
        $existingViewTypes = $inventory->images()->pluck('view_type')->toArray();

        $duplicates = array_intersect($requestViewTypes, $existingViewTypes);
        if (!empty($duplicates)) {
            return response()->json([
                'message' => 'The following view types already exist for this item: ' . implode(', ', $duplicates),
                'duplicate_views' => array_values($duplicates)
            ], 422);
        }

        $uploadedImages = [];
        $existingImagesCount = $inventory->images()->count();

        // Check if this is the first image to set as primary (only if no is_primary flag is explicitly set)
        $isPrimaryFlags = $request->is_primary ?? [];
        $hasExplicitPrimary = in_array('1', $isPrimaryFlags, true);
        $shouldAutoSetPrimary = $existingImagesCount === 0 && !$hasExplicitPrimary;

        // If a new image is explicitly set as primary, unset all existing primary flags
        if ($hasExplicitPrimary) {
            $inventory->images()->update(['is_primary' => false]);
        }

        foreach ($request->file('images') as $index => $image) {
            // Generate unique filename
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();

            // Store image in public disk under inventory folder
            $path = $image->storeAs('inventory/' . $inventory->item_id, $filename, 'public');

            // Get view type and caption
            $viewType = $request->view_types[$index];
            $caption = $request->captions[$index] ?? null;
            
            // Determine if this image should be primary
            $isPrimary = false;
            if ($hasExplicitPrimary) {
                // Use explicit is_primary flag from request
                $isPrimary = isset($isPrimaryFlags[$index]) && $isPrimaryFlags[$index] === '1';
            } else {
                // Auto-set first image as primary if no images exist
                $isPrimary = $shouldAutoSetPrimary && $index === 0;
            }

            // Create image record
            $inventoryImage = $inventory->images()->create([
                'image_path' => $path,
                'image_url' => Storage::url($path),
                'view_type' => $viewType,
                'caption' => $caption,
                'is_primary' => $isPrimary,
                'display_order' => $existingImagesCount + $index + 1,
                'file_size' => $image->getSize(),
                'mime_type' => $image->getMimeType()
            ]);

            $uploadedImages[] = $inventoryImage;
        }

        // Check if all required views are now complete
        $missingViews = $this->getMissingRequiredViews($inventory);
        $isComplete = empty($missingViews);

        return response()->json([
            'message' => count($uploadedImages) . ' image(s) uploaded successfully',
            'data' => $uploadedImages,
            'inventory_images_complete' => $isComplete,
            'missing_required_views' => $missingViews
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

        // If changing view_type, check for duplicates
        if ($request->has('view_type') && $request->view_type !== $image->view_type) {
            $existsViewType = $inventory->images()
                ->where('view_type', $request->view_type)
                ->where('image_id', '!=', $image->image_id)
                ->exists();

            if ($existsViewType) {
                return response()->json([
                    'message' => "An image with view type '{$request->view_type}' already exists for this item"
                ], 422);
            }
        }

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
        $viewType = $image->view_type;

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

        // Check remaining required views
        $missingViews = $this->getMissingRequiredViews($inventory);

        return response()->json([
            'message' => 'Image deleted successfully',
            'deleted_view_type' => $viewType,
            'missing_required_views' => $missingViews
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

        // Delete the entire folder
        $folderPath = 'inventory/' . $inventory->item_id;
        if (Storage::disk('public')->exists($folderPath)) {
            Storage::disk('public')->deleteDirectory($folderPath);
        }

        return response()->json([
            'message' => 'All images deleted successfully',
            'missing_required_views' => ['front', 'back', 'side']
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
            // If no primary image set, get the first one by display order
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
            'data' => $images,
            'view_type' => $viewType,
            'count' => $images->count()
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

        $missingViews = $this->getMissingRequiredViews($inventory);

        return response()->json([
            'message' => "{$deletedCount} image(s) deleted successfully",
            'deleted_count' => $deletedCount,
            'missing_required_views' => $missingViews
        ]);
    }

    /**
     * Validate that required views (front, back, side) are present
     */
    public function validateRequiredViews(Inventory $inventory): JsonResponse
    {
        $missingViews = $this->getMissingRequiredViews($inventory);
        $isComplete = empty($missingViews);

        $images = $inventory->images()
            ->orderBy('is_primary', 'desc')
            ->orderBy('display_order', 'asc')
            ->get();

        return response()->json([
            'is_complete' => $isComplete,
            'missing_views' => $missingViews,
            'existing_views' => $images->pluck('view_type')->toArray(),
            'total_images' => $images->count(),
            'message' => $isComplete
                ? 'All required image views are present'
                : 'Missing required views: ' . implode(', ', $missingViews)
        ]);
    }

    /**
     * Upload or update specific view type images
     */
    public function uploadByViewType(Request $request, Inventory $inventory, string $viewType): JsonResponse
    {
        $validViewTypes = ['front', 'back', 'side', 'detail', 'full'];

        if (!in_array($viewType, $validViewTypes)) {
            return response()->json([
                'message' => 'Invalid view type. Must be one of: ' . implode(', ', $validViewTypes)
            ], 400);
        }

        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'caption' => 'nullable|string|max:255',
            'replace_existing' => 'nullable|boolean'
        ]);

        $replaceExisting = $request->get('replace_existing', false);

        // Check if image with this view type already exists
        $existingImage = $inventory->images()
            ->where('view_type', $viewType)
            ->first();

        if ($existingImage && !$replaceExisting) {
            return response()->json([
                'message' => "An image with view type '{$viewType}' already exists. Set 'replace_existing' to true to replace it.",
                'existing_image' => $existingImage
            ], 422);
        }

        $image = $request->file('image');
        $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('inventory/' . $inventory->item_id, $filename, 'public');

        if ($existingImage && $replaceExisting) {
            // Delete old file
            if (Storage::disk('public')->exists($existingImage->image_path)) {
                Storage::disk('public')->delete($existingImage->image_path);
            }

            // Update existing record
            $existingImage->update([
                'image_path' => $path,
                'image_url' => Storage::url($path),
                'caption' => $request->get('caption', $existingImage->caption),
                'file_size' => $image->getSize(),
                'mime_type' => $image->getMimeType()
            ]);

            $inventoryImage = $existingImage;
            $message = "Image for view type '{$viewType}' replaced successfully";
        } else {
            // Create new record
            $existingCount = $inventory->images()->count();
            $shouldSetPrimary = $existingCount === 0;

            $inventoryImage = $inventory->images()->create([
                'image_path' => $path,
                'image_url' => Storage::url($path),
                'view_type' => $viewType,
                'caption' => $request->get('caption'),
                'is_primary' => $shouldSetPrimary,
                'display_order' => $existingCount + 1,
                'file_size' => $image->getSize(),
                'mime_type' => $image->getMimeType()
            ]);

            $message = "Image for view type '{$viewType}' uploaded successfully";
        }

        $missingViews = $this->getMissingRequiredViews($inventory);

        return response()->json([
            'message' => $message,
            'data' => $inventoryImage,
            'missing_required_views' => $missingViews,
            'inventory_images_complete' => empty($missingViews)
        ], $existingImage ? 200 : 201);
    }

    /**
     * Helper: Get missing required views for an inventory item
     */
    private function getMissingRequiredViews(Inventory $inventory): array
    {
        $requiredViews = ['front', 'back', 'side'];
        $existingViews = $inventory->images()->pluck('view_type')->toArray();

        return array_values(array_diff($requiredViews, $existingViews));
    }
}
