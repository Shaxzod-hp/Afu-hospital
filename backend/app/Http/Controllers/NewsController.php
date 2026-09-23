<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Support\Slug;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    /**
     * Admin marshrutlari (/api/admin/...) orqali kelgan so'rovmi?
     * Admin marshrutlari auth:sanctum + admin middleware bilan himoyalangan.
     */
    private function isAdminRequest(Request $request): bool
    {
        return $request->is('api/admin/*');
    }

    // 1. Barcha yangiliklar
    public function index(Request $request): JsonResponse
    {
        $query = News::with('category')->latest('published_at')->latest('id');

        if (!$this->isAdminRequest($request)) {
            $query->where('is_published', true);
        }

        // Admin ro'yxati barcha yangiliklarni bir sahifada ko'rsatadi
        $maxPerPage = $this->isAdminRequest($request) ? 1000 : 100;
        $perPage = min(max((int) $request->input('per_page', 12), 1), $maxPerPage);

        return response()->json($query->paginate($perPage));
    }

    private function findNews(Request $request, $idOrSlug): News
    {
        $query = News::with('category')->where(ctype_digit((string) $idOrSlug) ? 'id' : 'slug', $idOrSlug);

        if (!$this->isAdminRequest($request)) {
            $query->where('is_published', true);
        }

        return $query->firstOrFail();
    }

    // 2. ID yoki slug bo'yicha bitta yangilikni ko'rish
    public function show(Request $request, $idOrSlug): JsonResponse
    {
        $news = $this->findNews($request, $idOrSlug);

        // Admin tahrirlash sahifasini ochganda ko'rishlar soni oshmasin
        if (!$this->isAdminRequest($request)) {
            $news->increment('views');
        }

        return response()->json($news);
    }

    private function rules(bool $isUpdate): array
    {
        $req = $isUpdate ? 'sometimes|required' : 'required';

        return [
            'title' => "$req|string|max:255",
            'slug' => 'nullable|string|max:255',
            'category_id' => "$req|exists:news_categories,id",
            'summary' => 'nullable|string',
            'content' => "$req|string",
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'is_published' => 'sometimes|boolean',
            'published_at' => 'nullable|date',
        ];
    }

    // 3. Yangilik yaratish
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules(false));

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/news', 'public');
            $validated['image'] = '/storage/' . $path;
        }

        $validated['slug'] = Slug::unique(News::class, $validated['slug'] ?? $validated['title']);
        $validated['published_at'] = $validated['published_at'] ?? now();

        $news = News::create($validated);

        return response()->json(['message' => 'Yangilik muvaffaqiyatli yaratildi', 'data' => $news], 201);
    }

    // 4. ID yoki slug bo'yicha yangilash
    public function update(Request $request, $idOrSlug): JsonResponse
    {
        $news = $this->findNews($request, $idOrSlug);

        $validated = $request->validate($this->rules(true));

        $oldImage = null;
        if ($request->hasFile('image')) {
            $oldImage = $news->image;
            $path = $request->file('image')->store('uploads/news', 'public');
            $validated['image'] = '/storage/' . $path;
        } else {
            unset($validated['image']);
        }

        if (!empty($validated['slug'])) {
            $validated['slug'] = Slug::unique(News::class, $validated['slug'], $news->id);
        } elseif (isset($validated['title']) && $validated['title'] !== $news->title) {
            $validated['slug'] = Slug::unique(News::class, $validated['title'], $news->id);
        } else {
            unset($validated['slug']);
        }

        if (array_key_exists('published_at', $validated) && $validated['published_at'] === null) {
            unset($validated['published_at']);
        }

        $news->update($validated);

        if ($oldImage && str_starts_with($oldImage, '/storage/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $oldImage));
        }

        return response()->json(['message' => 'Yangilik yangilandi', 'data' => $news]);
    }

    // 5. ID yoki slug bo'yicha o'chirish
    public function destroy(Request $request, $idOrSlug): JsonResponse
    {
        $news = $this->findNews($request, $idOrSlug);

        if ($news->image && str_starts_with($news->image, '/storage/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $news->image));
        }

        $news->delete();

        return response()->json(['message' => 'Yangilik o\'chirildi']);
    }
}
