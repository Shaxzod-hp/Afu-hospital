<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatmentLog extends Model
{
    use HasFactory;

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
}
