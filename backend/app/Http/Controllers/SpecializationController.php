<?php

namespace App\Http\Controllers;

use App\Models\Specialization;
use App\Support\Slug;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            'name' => 'required|string|min:2|max:255|unique:specializations,name',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['slug'] = Slug::unique(Specialization::class, $validated['slug'] ?? $validated['name']);

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
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        if (!empty($validated['slug'])) {
            $validated['slug'] = Slug::unique(Specialization::class, $validated['slug'], $specialization->id);
        } elseif (isset($validated['name']) && $validated['name'] !== $specialization->name) {
            $validated['slug'] = Slug::unique(Specialization::class, $validated['name'], $specialization->id);
        } else {
            unset($validated['slug']);
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

        if ($specialization->doctors()->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Bu mutaxassislikka biriktirilgan shifokorlar bor, avval ularni boshqa mutaxassislikka o'tkazing.",
            ], 422);
        }

        $specialization->delete();

        return response()->json([
            'success' => true,
            'message' => 'Specialization deleted successfully.',
        ]);
    }
}