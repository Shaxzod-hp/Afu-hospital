<?php

namespace App\Support;

use Illuminate\Support\Str;

class Slug
{
    /**
     * Berilgan model jadvali uchun unikal slug yaratadi: "nom", "nom-1", "nom-2", ...
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $modelClass
     */
    public static function unique(string $modelClass, string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source);

        // Faqat raqamdan iborat slug ID bilan adashtiriladi (findX() is_numeric tekshiradi)
        if ($base === '' || ctype_digit($base)) {
            $base = trim('item-' . $base, '-');
        }

        $slug = $base;
        $count = 1;

        while (
            $modelClass::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$count}";
            $count++;
        }

        return $slug;
    }
}
