<?php

namespace App\Http\Controllers;

use App\Models\StatsionarPackage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StatsionarPackageController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => StatsionarPackage::latest()->paginate(20),
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
            'name' => 'required|string|min:2',
            'slug' => 'nullable|string|unique:statsionar_packages,slug',
            'note' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'included_items' => 'nullable|array',
            'included_items.*' => 'string',
            'photos' => 'required|array|min:2',
            'photos.*' => 'image|mimes:jpeg,jpg,png,webp|max:5120',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

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
            'name' => 'sometimes|required|string|min:2',
            'slug' => 'nullable|string|unique:statsionar_packages,slug,' . $package->id,
            'note' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'included_items' => 'nullable|array',
            'included_items.*' => 'string',
            'photos' => 'nullable|array|min:2',
            'photos.*' => 'image|mimes:jpeg,jpg,png,webp|max:5120',
        ]);

        if (isset($validated['name']) && empty($validated['slug']) && $validated['name'] !== $package->name) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Yangi rasm yuklansa, eskilari ochiriladi
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