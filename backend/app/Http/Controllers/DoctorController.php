<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Support\Slug;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class DoctorController extends Controller
{
    private const DAYS = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

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
            'data' => $query->get(),
        ]);
    }

    /**
     * ID bo'yicha ham, Slug bo'yicha ham aniq topish uchun
     */
    public static function findDoctor($idOrSlug): Doctor
    {
        return Doctor::with(['specialization', 'schedules'])
            ->where(function ($q) use ($idOrSlug) {
                if (ctype_digit((string) $idOrSlug)) {
                    $q->where('id', (int) $idOrSlug)->orWhere('slug', (string) $idOrSlug);
                } else {
                    $q->where('slug', (string) $idOrSlug);
                }
            })
            ->firstOrFail();
    }

    public function show($idOrSlug): JsonResponse
    {
        $doctor = self::findDoctor($idOrSlug);

        return response()->json([
            'success' => true,
            'data' => $doctor,
        ]);
    }

    private function rules(?Doctor $doctor = null): array
    {
        return [
            'full_name' => ($doctor ? 'sometimes|' : '') . 'required|string|min:2|max:255',
            'slug' => 'nullable|string|max:255',
            'specialization_id' => ($doctor ? 'sometimes|' : '') . 'required|exists:specializations,id',
            'position' => 'nullable|string|max:255',
            'experience_years' => 'nullable|integer|min:0|max:70',
            'bio' => 'nullable|string',
            'education' => 'nullable|string',
            'previous_workplace' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'working_hours' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'telegram' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'schedule' => 'nullable|json', // FormData'dan keladigan JSON string
        ];
    }

    /**
     * Frontend yuboradigan { monday: { active, start, end }, ... } obyektini
     * tekshirib, doctor_schedules jadvaliga yoziladigan qatorlarga aylantiradi.
     */
    private function parseSchedule(?string $json): ?array
    {
        if ($json === null) {
            return null;
        }

        $schedule = json_decode($json, true);
        if (!is_array($schedule)) {
            throw ValidationException::withMessages(['schedule' => ["Ish jadvali formati noto'g'ri."]]);
        }

        $rows = [];
        foreach ($schedule as $day => $info) {
            if (!in_array($day, self::DAYS, true) || !is_array($info) || empty($info['active'])) {
                continue;
            }

            $start = $info['start'] ?? '09:00';
            $end = $info['end'] ?? '18:00';

            if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $start) || !preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $end)) {
                throw ValidationException::withMessages(['schedule' => ["Ish vaqti HH:MM formatida bo'lishi kerak."]]);
            }
            if ($start >= $end) {
                throw ValidationException::withMessages(['schedule' => ["Ish tugash vaqti boshlanish vaqtidan keyin bo'lishi kerak."]]);
            }

            $rows[] = ['day' => $day, 'start_time' => $start, 'end_time' => $end, 'is_available' => true];
        }

        return $rows;
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules());
        $scheduleRows = $this->parseSchedule($validated['schedule'] ?? null);
        unset($validated['schedule']);

        $validated['slug'] = Slug::unique(Doctor::class, $validated['slug'] ?? $validated['full_name']);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('uploads/doctors', 'public');
            $validated['photo'] = '/storage/' . $path;
        }

        $doctor = DB::transaction(function () use ($validated, $scheduleRows) {
            $doctor = Doctor::create($validated);
            if ($scheduleRows) {
                $doctor->schedules()->createMany($scheduleRows);
            }
            return $doctor;
        });

        return response()->json([
            'success' => true,
            'message' => 'Doctor created successfully.',
            'data' => $doctor->load(['specialization', 'schedules']),
        ], 201);
    }

    public function update(Request $request, $idOrSlug): JsonResponse
    {
        $doctor = self::findDoctor($idOrSlug);

        $validated = $request->validate($this->rules($doctor));
        $hasSchedule = array_key_exists('schedule', $validated) && $validated['schedule'] !== null;
        $scheduleRows = $hasSchedule ? $this->parseSchedule($validated['schedule']) : null;
        unset($validated['schedule']);

        if (!empty($validated['slug'])) {
            $validated['slug'] = Slug::unique(Doctor::class, $validated['slug'], $doctor->id);
        } elseif (isset($validated['full_name']) && $validated['full_name'] !== $doctor->full_name) {
            $validated['slug'] = Slug::unique(Doctor::class, $validated['full_name'], $doctor->id);
        } else {
            unset($validated['slug']);
        }

        $oldPhoto = null;
        if ($request->hasFile('photo')) {
            $oldPhoto = $doctor->photo;
            $path = $request->file('photo')->store('uploads/doctors', 'public');
            $validated['photo'] = '/storage/' . $path;
        } else {
            unset($validated['photo']);
        }

        DB::transaction(function () use ($doctor, $validated, $hasSchedule, $scheduleRows) {
            $doctor->update($validated);

            // Yangi jadval yuborilgan bo'lsa, eskisini almashtiramiz
            if ($hasSchedule) {
                $doctor->schedules()->delete();
                if ($scheduleRows) {
                    $doctor->schedules()->createMany($scheduleRows);
                }
            }
        });

        if ($oldPhoto && str_starts_with($oldPhoto, '/storage/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $oldPhoto));
        }

        return response()->json([
            'success' => true,
            'message' => 'Doctor updated successfully.',
            'data' => $doctor->load(['specialization', 'schedules']),
        ]);
    }

    public function destroy($idOrSlug): JsonResponse
    {
        $doctor = self::findDoctor($idOrSlug);

        $files = [];
        if ($doctor->photo) {
            $files[] = $doctor->photo;
        }
        foreach ($doctor->treatmentLogs as $log) {
            foreach (($log->photos ?? []) as $photo) {
                $files[] = $photo;
            }
        }

        // treatment_logs va doctor_schedules FK orqali cascade o'chadi
        $doctor->delete();

        foreach ($files as $file) {
            if (str_starts_with($file, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $file));
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Doctor deleted successfully.',
        ]);
    }
}
