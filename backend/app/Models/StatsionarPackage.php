<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatsionarPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'photos',
        'included_items',
        'note',
        'price',
    ];

    protected $casts = [
        'photos' => 'array',
        'included_items' => 'array',
        'price' => 'decimal:2',
    ];

    /**
     * Marshrutlarda id o'rniga slug ishlatish uchun.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}