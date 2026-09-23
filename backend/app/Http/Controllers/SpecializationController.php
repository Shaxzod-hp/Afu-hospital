<?php

namespace App\Http\Controllers;

use App\Models\Specialization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SpecializationController extends Controller
{
    public function index(): JsonResponse
    {
        $specializations = Specialization::all();

        return response()->json([
            'success' => true,
            'data' => $specializations,
        ]);
    }

    private function findSpecialization($idOrSlug): Specialization
    {
        return Specialization::where(is_numeric($idOrSlug) ? 'id' : 'slug', $idOrSlug)->firstOrFail();
    }

    public function show($idOrSlug): JsonResponse
    {
        $specialization = $this->findSpecialization($idOrSlug);

        return response()->json([
            'success' => true,
            'data' => $specialization,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|unique:specializations,name',
            'slug' => 'nullable|string|unique:specializations,slug',
            'description' => 'nullable|string',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $specialization = Specialization::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Specialization created successfully.',
            'data' => $specialization,
        ], 201);
    }

    public function update(Request $request, $idOrSlug): JsonResponse
    {
        $specialization = $this->findSpecialization($idOrSlug);

        $validated = $request->validate([
            'name' => [
                'sometimes',
                'required',
                'string',
                'min:2',
                Rule::unique('specializations', 'name')->ignore($specialization->id),
            ],
            'slug' => [
                'nullable',
                'string',
                Rule::unique('specializations', 'slug')->ignore($specialization->id),
            ],
            'description' => 'nullable|string',
        ]);

        if (isset($validated['name']) && empty($validated['slug']) && $validated['name'] !== $specialization->name) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $specialization->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Specialization updated successfully.',
            'data' => $specialization,
        ]);
    }

    public function destroy($idOrSlug): JsonResponse
    {
        $specialization = $this->findSpecialization($idOrSlug);

        if (method_exists($specialization, 'doctors') && $specialization->doctors() && $specialization->doctors()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Specialization cannot be deleted because it has associated doctors.',
            ], 400);
        }

        if (method_exists($specialization, 'operations') && $specialization->operations() && $specialization->operations()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Specialization cannot be deleted because it has associated operations.',
            ], 400);
        }

        $specialization->delete();

        return response()->json([
            'success' => true,
            'message' => 'Specialization deleted successfully.',
        ]);
    }
}