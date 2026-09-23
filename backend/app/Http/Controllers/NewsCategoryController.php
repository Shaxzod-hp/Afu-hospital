<?php

namespace App\Http\Controllers;

use App\Models\NewsCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Support\Slug;

class NewsCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => NewsCategory::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255|unique:news_categories,name',
        ]);

        $validated['slug'] = Slug::unique(NewsCategory::class, $validated['name']);

        $category = NewsCategory::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kategoriya yaratildi.',
            'data' => $category,
        ], 201);
    }
}