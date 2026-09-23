<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    // 1. Barcha yangiliklar
    public function index(Request $request)
    {
        try {
            $query = News::with('category')->latest();

            $isAdmin = $request->user() && $request->user()->role === 'admin';
            if (!$isAdmin) {
                $query->where('is_published', true);
            }

            $news = $query->paginate(12);

            return response()->json($news, 200);
        } catch (\Exception $e) {
            \Log::error('News fetch error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Serverda xatolik yuz berdi. Iltimos keyinroq urinib ko\'ring.',
            ], 500);
        }
    }

    private function findNews($idOrSlug): News
    {
        return News::where(is_numeric($idOrSlug) ? 'id' : 'slug', $idOrSlug)->firstOrFail();
    }

    /**
     * Unikal va toza slug yaratuvchi yordamchi usul
     */
    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (
            News::where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }

    // 2. ID yoki slug bo'yicha bitta yangilikni ko'rish
    public function show($idOrSlug)
    {
        $news = $this->findNews($idOrSlug);
        $news->increment('views');

        return response()->json($news);
    }

    // 3. Yangilik yaratish
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:news,slug',
            'category_id' => 'required|exists:news_categories,id',
            'summary' => 'nullable|string',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_published' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            // Faylni to'g'ridan-to'g'ri storage/app/public/uploads/news papkasiga saqlaymiz
            $path = $request->file('image')->store('uploads/news', 'public');
            $validated['image'] = '/storage/' . $path;
        }

        if (empty($validated['slug'])) {
            $validated['slug'] = $this->generateUniqueSlug($validated['title']);
        } else {
            $validated['slug'] = Str::slug($validated['slug']);
        }

        $validated['published_at'] = now();

        $news = News::create($validated);

        return response()->json(['message' => 'Yangilik muvaffaqiyatli yaratildi', 'data' => $news], 201);
    }

    // 4. ID yoki slug bo'yicha yangilash
    public function update(Request $request, $idOrSlug)
    {
        $news = $this->findNews($idOrSlug);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'slug' => 'nullable|string|unique:news,slug,' . $news->id,
            'category_id' => 'sometimes|required|exists:news_categories,id',
            'summary' => 'nullable|string',
            'content' => 'sometimes|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_published' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($news->image) {
                // /storage/ qismini olib tashlab, diskdan to'g'ri o'chiramiz
                $oldPath = str_replace('/storage/', '', $news->image);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('image')->store('uploads/news', 'public');
            $validated['image'] = '/storage/' . $path;
        }

        if (isset($validated['slug']) && !empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['slug']);
        } elseif (isset($validated['title']) && $validated['title'] !== $news->title) {
            $validated['slug'] = $this->generateUniqueSlug($validated['title'], $news->id);
        }

        $news->update($validated);

        return response()->json(['message' => 'Yangilik yangilandi', 'data' => $news]);
    }

    // 5. ID yoki slug bo'yicha o'chirish
    public function destroy($idOrSlug)
    {
        $news = $this->findNews($idOrSlug);

        if ($news->image) {
            $oldPath = str_replace('/storage/', '', $news->image);
            Storage::disk('public')->delete($oldPath);
        }

        $news->delete();

        return response()->json(['message' => 'Yangilik o\'chirildi']);
    }
}