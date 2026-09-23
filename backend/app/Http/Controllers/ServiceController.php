<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Service::latest()->paginate(20),
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
            'name' => 'required|string|min:2',
            'slug' => 'nullable|string|unique:services,slug',
            'short_description' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'included_items' => 'nullable|array',
            'included_items.*.name' => 'required_with:included_items|string',
            'included_items.*.price' => 'required_with:included_items|numeric|min:0',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

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
            'name' => 'sometimes|required|string|min:2',
            'slug' => 'nullable|string|unique:services,slug,' . $service->id,
            'short_description' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'included_items' => 'nullable|array',
            'included_items.*.name' => 'required_with:included_items|string',
            'included_items.*.price' => 'required_with:included_items|numeric|min:0',
        ]);

        if (isset($validated['name']) && empty($validated['slug']) && $validated['name'] !== $service->name) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        if ($request->hasFile('photo')) {
            if ($service->photo && str_starts_with($service->photo, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $service->photo));
            }
            $path = $request->file('photo')->store('uploads/services', 'public');
            $validated['photo'] = '/storage/' . $path;
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