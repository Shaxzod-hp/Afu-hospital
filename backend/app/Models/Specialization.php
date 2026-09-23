<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Marshrutlarda id o'rniga slug ishlatish uchun.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Relationship to Doctors.
     */
    public function doctors()
    {
        return $this->hasMany(\App\Models\Doctor::class);
    }
}