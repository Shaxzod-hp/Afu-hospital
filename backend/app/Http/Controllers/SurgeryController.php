<?php

namespace App\Http\Controllers;

use App\Models\Surgery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SurgeryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Surgery::latest()->paginate(20),
        ]);
    }

    private function findSurgery($idOrSlug): Surgery
    {
        return Surgery::where(is_numeric($idOrSlug) ? 'id' : 'slug', $idOrSlug)->firstOrFail();
    }

    public function show($idOrSlug): JsonResponse
    {
        $surgery = $this->findSurgery($idOrSlug);

        return response()->json([
            'success' => true,
            'data' => $surgery,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2',
            'slug' => 'nullable|string|unique:surgeries,slug',
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
            $path = $request->file('photo')->store('uploads/surgeries', 'public');
            $validated['photo'] = '/storage/' . $path;
        }

        $surgery = Surgery::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Surgery created successfully.',
            'data' => $surgery,
        ], 201);
    }

    public function update(Request $request, $idOrSlug): JsonResponse
    {
        $surgery = $this->findSurgery($idOrSlug);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|min:2',
            'slug' => 'nullable|string|unique:surgeries,slug,' . $surgery->id,
            'short_description' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'included_items' => 'nullable|array',
            'included_items.*.name' => 'required_with:included_items|string',
            'included_items.*.price' => 'required_with:included_items|numeric|min:0',
        ]);

        if (isset($validated['name']) && empty($validated['slug']) && $validated['name'] !== $surgery->name) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        if ($request->hasFile('photo')) {
            if ($surgery->photo && str_starts_with($surgery->photo, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $surgery->photo));
            }
            $path = $request->file('photo')->store('uploads/surgeries', 'public');
            $validated['photo'] = '/storage/' . $path;
        }

        $surgery->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Surgery updated successfully.',
            'data' => $surgery,
        ]);
    }

    public function destroy($idOrSlug): JsonResponse
    {
        $surgery = $this->findSurgery($idOrSlug);

        if ($surgery->photo && str_starts_with($surgery->photo, '/storage/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $surgery->photo));
        }

        $surgery->delete();

        return response()->json([
            'success' => true,
            'message' => 'Surgery deleted successfully.',
        ]);
    }
}