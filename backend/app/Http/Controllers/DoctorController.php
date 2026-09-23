<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DoctorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Doctor::with(['specialization', 'schedules'])->latest();

        // Mutaxassislik bo'yicha filtrlash (ID yoki Slug orqali)
        if ($request->filled('specialization_id')) {
            $query->where('specialization_id', $request->input('specialization_id'));
        } elseif ($request->filled('specialization_slug')) {
            $query->whereHas('specialization', function ($q) use ($request) {
                $q->where('slug', $request->input('specialization_slug'));
            });
        }

        return response()->json([
            'success' => true,
            'data' => $query->paginate(20),
        ]);
    }

    /**
     * ID bo'yicha ham, Slug bo'yicha ham aniq topish uchun
     */
    private function findDoctor($idOrSlug): Doctor
    {
        return Doctor::with(['specialization', 'schedules'])
            ->where(function ($q) use ($idOrSlug) {
                if (is_numeric($idOrSlug)) {
                    $q->where('id', $idOrSlug)->orWhere('slug', (string) $idOrSlug);
                } else {
                    $q->where('slug', $idOrSlug);
                }
            })
            ->firstOrFail();
    }

    public function show($idOrSlug): JsonResponse
    {
        $doctor = $this->findDoctor($idOrSlug);

        return response()->json([
            'success' => true,
            'data' => $doctor,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'full_name' => 'required|string|min:2',
            'slug' => 'nullable|string|unique:doctors,slug',
            'specialization_id' => 'required|exists:specializations,id',
            'position' => 'nullable|string',
            'experience_years' => 'nullable|integer|min:5|max:60',
            'bio' => 'nullable|string',
            'education' => 'nullable|string',
            'previous_workplace' => 'nullable|string',
            'phone' => 'nullable|string',
            'working_hours' => 'nullable|string',
            'instagram' => 'nullable|string',
            'telegram' => 'nullable|string',
            'whatsapp' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'schedule' => 'nullable', // FormData'dan keladigan JSON string
        ]);

        // Slug bo'sh bo'lsa me'yoriy unikallashtirib berish
        if (empty($validated['slug'])) {
            $baseSlug = Str::slug($validated['full_name']);
            $slug = $baseSlug;
            $count = 1;

            while (Doctor::where('slug', $slug)->exists()) {
                $slug = "{$baseSlug}-{$count}";
                $count++;
            }
            $validated['slug'] = $slug;
        }

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('uploads/doctors', 'public');
            $validated['photo'] = '/storage/' . $path;
        }

        // Schedule ma'lumotlarini alohida ajratib olamiz
        $scheduleData = $validated['schedule'] ?? null;
        unset($validated['schedule']); // Doctors jadvalida bu ustun bo'lmagani uchun olib tashlaymiz

        // Shifokorni yaratish
        $doctor = Doctor::create($validated);

        // Ish vaqtlarini saqlash (doctor_schedules jadvaliga)
        if ($scheduleData) {
            $schedules = is_string($scheduleData) ? json_decode($scheduleData, true) : $scheduleData;

            if (is_array($schedules)) {
                foreach ($schedules as $day => $info) {
                    if (isset($info['active']) && $info['active'] === true) {
                        DoctorSchedule::create([
                            'doctor_id' => $doctor->id,
                            'day' => $day,
                            'start_time' => $info['start'] ?? '09:00',
                            'end_time' => $info['end'] ?? '16:00',
                            'is_available' => true,
                        ]);
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Doctor created successfully.',
            'data' => $doctor->load(['specialization', 'schedules']),
        ], 201);
    }

    public function update(Request $request, $idOrSlug): JsonResponse
    {
        $doctor = $this->findDoctor($idOrSlug);

        $validated = $request->validate([
            'full_name' => 'sometimes|required|string|min:2',
            'slug' => 'nullable|string|unique:doctors,slug,' . $doctor->id,
            'specialization_id' => 'sometimes|required|exists:specializations,id',
            'position' => 'nullable|string',
            'experience_years' => 'nullable|integer|min:5|max:60',
            'bio' => 'nullable|string',
            'education' => 'nullable|string',
            'previous_workplace' => 'nullable|string',
            'phone' => 'nullable|string',
            'working_hours' => 'nullable|string',
            'instagram' => 'nullable|string',
            'telegram' => 'nullable|string',
            'whatsapp' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'schedule' => 'nullable',
        ]);

        if (isset($validated['full_name']) && empty($validated['slug']) && $validated['full_name'] !== $doctor->full_name) {
            $baseSlug = Str::slug($validated['full_name']);
            $slug = $baseSlug;
            $count = 1;

            while (Doctor::where('slug', $slug)->where('id', '!=', $doctor->id)->exists()) {
                $slug = "{$baseSlug}-{$count}";
                $count++;
            }
            $validated['slug'] = $slug;
        }

        if ($request->hasFile('photo')) {
            if ($doctor->photo && str_starts_with($doctor->photo, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $doctor->photo));
            }
            $path = $request->file('photo')->store('uploads/doctors', 'public');
            $validated['photo'] = '/storage/' . $path;
        }

        // Schedule ma'lumotlarini ajratish
        $scheduleData = $validated['schedule'] ?? null;
        unset($validated['schedule']);

        $doctor->update($validated);

        // Agar yangi schedule yuborilgan bo'lsa, eskisini o'chirib, yangisini yozamiz
        if ($scheduleData !== null) {
            $doctor->schedules()->delete(); // Eskilarini tozalash

            $schedules = is_string($scheduleData) ? json_decode($scheduleData, true) : $scheduleData;

            if (is_array($schedules)) {
                foreach ($schedules as $day => $info) {
                    if (isset($info['active']) && $info['active'] === true) {
                        DoctorSchedule::create([
                            'doctor_id' => $doctor->id,
                            'day' => $day,
                            'start_time' => $info['start'] ?? '09:00',
                            'end_time' => $info['end'] ?? '16:00',
                            'is_available' => true,
                        ]);
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Doctor updated successfully.',
            'data' => $doctor->load(['specialization', 'schedules']),
        ]);
    }

    public function destroy($idOrSlug): JsonResponse
    {
        $doctor = $this->findDoctor($idOrSlug);

        if ($doctor->photo && str_starts_with($doctor->photo, '/storage/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $doctor->photo));
        }

        $doctor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Doctor deleted successfully.',
        ]);
    }
}