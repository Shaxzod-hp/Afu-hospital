<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surgery extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'short_description',
        'photo',
        'included_services',
        'price',
        'slug'
    ];

    protected $casts = [
        'included_items' => 'array',
    ];
}