<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Support\Slug;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Service::latest()->get(),
        ]);
    }

    private function findService($idOrSlug): Service
    {
        return Service::where(is_numeric($idOrSlug) ? 'id' : 'slug', $idOrSlug)->firstOrFail();
    }

    public function show($idOrSlug): JsonResponse
    {
        $service = $this->findService($idOrSlug);

        return response()->json([
            'success' => true,
            'data' => $service,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'slug' => 'nullable|string|max:255',
            'short_description' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'included_items' => 'nullable|array',
            'included_items.*.name' => 'required_with:included_items|string|max:255',
            'included_items.*.price' => 'required_with:included_items|numeric|min:0',
        ]);

        $validated['slug'] = Slug::unique(Service::class, $validated['slug'] ?? $validated['name']);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('uploads/services', 'public');
            $validated['photo'] = '/storage/' . $path;
        }

        $service = Service::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Service created successfully.',
            'data' => $service,
        ], 201);
    }

    public function update(Request $request, $idOrSlug): JsonResponse
    {
        $service = $this->findService($idOrSlug);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|min:2|max:255',
            'slug' => 'nullable|string|max:255',
            'short_description' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'included_items' => 'nullable|array',
            'included_items.*.name' => 'required_with:included_items|string|max:255',
            'included_items.*.price' => 'required_with:included_items|numeric|min:0',
        ]);

        if (!empty($validated['slug'])) {
            $validated['slug'] = Slug::unique(Service::class, $validated['slug'], $service->id);
        } elseif (isset($validated['name']) && $validated['name'] !== $service->name) {
            $validated['slug'] = Slug::unique(Service::class, $validated['name'], $service->id);
        } else {
            unset($validated['slug']);
        }

        if ($request->hasFile('photo')) {
            if ($service->photo && str_starts_with($service->photo, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $service->photo));
            }
            $path = $request->file('photo')->store('uploads/services', 'public');
            $validated['photo'] = '/storage/' . $path;
        } else {
            unset($validated['photo']);
        }

        $service->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Service updated successfully.',
            'data' => $service,
        ]);
    }

    public function destroy($idOrSlug): JsonResponse
    {
        $service = $this->findService($idOrSlug);

        if ($service->photo && str_starts_with($service->photo, '/storage/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $service->photo));
        }

        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully.',
        ]);
    }
}
