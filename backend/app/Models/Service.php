<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'photo',
        'included_items',
    ];

    protected $casts = [
        'included_items' => 'array',
    ];

    /**
     * Marshrutlarda id o'rniga slug ishlatish uchun.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}