<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category_id',
        'summary',
        'content',
        'image',
        'views',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Marshrutlarda ID o'rniga slug ishlatish uchun.
     */
    public function category()
    {
        return $this->belongsTo(NewsCategory::class, 'category_id');
    }
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}