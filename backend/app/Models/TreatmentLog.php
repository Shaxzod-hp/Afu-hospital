<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TreatmentLog extends Model
{
    use HasFactory;

    /** Lavha saytda necha soat ko'rinib turadi */
    public const LIFETIME_HOURS = 24;

    public const MIN_PHOTOS = 2;
    public const MAX_PHOTOS = 10;

    protected $fillable = [
        'doctor_id',
        'description',
        'photos',
    ];

    protected $casts = [
        'photos' => 'array',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /** Saytda hali ko'rinishi kerak bo'lgan (24 soat ichidagi) lavhalar */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('created_at', '>=', now()->subHours(self::LIFETIME_HOURS));
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('created_at', '<', now()->subHours(self::LIFETIME_HOURS));
    }

    /** "/storage/..." ko'rinishidagi rasm yo'llarini public diskdan o'chiradi */
    public static function deleteStoredPhotos(array $photos): void
    {
        $paths = collect($photos)
            ->filter(fn ($p) => is_string($p) && str_starts_with($p, '/storage/'))
            ->map(fn ($p) => substr($p, strlen('/storage/')))
            ->values()
            ->all();

        if ($paths) {
            Storage::disk('public')->delete($paths);
        }
    }

    /** Lavhani rasmlari bilan birga o'chiradi */
    public function deleteWithPhotos(): void
    {
        $photos = $this->photos ?? [];
        $this->delete();
        self::deleteStoredPhotos($photos);
    }
}
