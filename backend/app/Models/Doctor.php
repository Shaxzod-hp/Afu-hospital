<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'slug',
        'specialization_id',
        'position',
        'experience_years',
        'bio',
        'education',
        'previous_workplace',
        'phone',
        'working_hours',
        'instagram',
        'telegram',
        'whatsapp',
        'photo',
    ];

    protected $casts = [
        'experience_years' => 'integer',
    ];

    /**
     * Route Implicit Model Binding uchun slug'ni asosiy kalit sifatida belgilaymiz.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function specialization()
    {
        return $this->belongsTo(Specialization::class);
    }

    public function schedules()
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    public function treatmentLogs()
    {
        return $this->hasMany(TreatmentLog::class);
    }
}