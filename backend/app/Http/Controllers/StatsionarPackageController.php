<?php

namespace App\Http\Controllers;

use App\Models\StatsionarPackage;
use App\Support\Slug;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StatsionarPackageController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => StatsionarPackage::latest()->get(),
        ]);
    }

    private function findPackage($idOrSlug): StatsionarPackage
    {
        return StatsionarPackage::where(is_numeric($idOrSlug) ? 'id' : 'slug', $idOrSlug)->firstOrFail();
    }

    public function show($idOrSlug): JsonResponse
    {
        $package = $this->findPackage($idOrSlug);

        return response()->json([
            'success' => true,
            'data' => $package,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'slug' => 'nullable|string|max:255',
            'note' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'included_items' => 'nullable|array',
            'included_items.*' => 'string|max:255',
            'photos' => 'required|array|min:2',
            'photos.*' => 'image|mimes:jpeg,jpg,png,webp|max:5120',
        ]);

        $validated['slug'] = Slug::unique(StatsionarPackage::class, $validated['slug'] ?? $validated['name']);

        $photoPaths = [];
        foreach ($request->file('photos', []) as $file) {
            $path = $file->store('uploads/statsionar', 'public');
            $photoPaths[] = '/storage/' . $path;
        }
        $validated['photos'] = $photoPaths;

        $package = StatsionarPackage::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Statsionar package created successfully.',
            'data' => $package,
        ], 201);
    }

    public function update(Request $request, $idOrSlug): JsonResponse
    {
        $package = $this->findPackage($idOrSlug);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|min:2|max:255',
            'slug' => 'nullable|string|max:255',
            'note' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'included_items' => 'nullable|array',
            'included_items.*' => 'string|max:255',
            'photos' => 'nullable|array|min:2',
            'photos.*' => 'image|mimes:jpeg,jpg,png,webp|max:5120',
        ]);

        if (!empty($validated['slug'])) {
            $validated['slug'] = Slug::unique(StatsionarPackage::class, $validated['slug'], $package->id);
        } elseif (isset($validated['name']) && $validated['name'] !== $package->name) {
            $validated['slug'] = Slug::unique(StatsionarPackage::class, $validated['name'], $package->id);
        } else {
            unset($validated['slug']);
        }

        // Yangi rasm yuklansa, eskilari o'chiriladi
        if ($request->hasFile('photos')) {
            foreach (($package->photos ?? []) as $oldPhoto) {
                if (str_starts_with($oldPhoto, '/storage/')) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $oldPhoto));
                }
            }

            $photoPaths = [];
            foreach ($request->file('photos', []) as $file) {
                $path = $file->store('uploads/statsionar', 'public');
                $photoPaths[] = '/storage/' . $path;
            }
            $validated['photos'] = $photoPaths;
        } else {
            unset($validated['photos']);
        }

        $package->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Statsionar package updated successfully.',
            'data' => $package,
        ]);
    }

    public function destroy($idOrSlug): JsonResponse
    {
        $package = $this->findPackage($idOrSlug);

        foreach (($package->photos ?? []) as $photo) {
            if (str_starts_with($photo, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $photo));
            }
        }

        $package->delete();

        return response()->json([
            'success' => true,
            'message' => 'Statsionar package deleted successfully.',
        ]);
    }
}
