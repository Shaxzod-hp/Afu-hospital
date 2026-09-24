<?php

namespace App\Http\Controllers;

use App\Models\TreatmentLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TreatmentLogController extends Controller
{
    // Admin: list all logs, optionally filtered by doctor
    public function index(Request $request): JsonResponse
    {
        $request->validate(['doctor_id' => 'nullable|integer']);

        $query = TreatmentLog::with('doctor:id,full_name,slug')->latest();

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        return response()->json([
            'success' => true,
            'data' => $query->get(),
        ]);
    }

    // Public: today's active logs for one doctor (slug yoki id orqali ishlashi uchun)
    public function forDoctor($idOrSlug): JsonResponse
    {
        // Shifokorni ID yoki Slug bo'yicha topamiz ("5-ali" slug'i ID 5 ga aylanib ketmasligi uchun)
        $doctor = DoctorController::findDoctor($idOrSlug);

        $logs = TreatmentLog::where('doctor_id', $doctor->id)
            ->active()
            ->latest()
            ->get(['id', 'doctor_id', 'description', 'photos', 'created_at']);

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $min = TreatmentLog::MIN_PHOTOS;
        $max = TreatmentLog::MAX_PHOTOS;

        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'description' => 'required|string|min:5|max:2000',
            'photos' => "required|array|min:{$min}|max:{$max}",
            'photos.*' => 'required|image|mimes:jpeg,jpg,png,webp|max:5120',
        ], [
            'doctor_id.required' => 'Shifokor tanlanmagan.',
            'doctor_id.exists' => 'Shifokor topilmadi.',
            'description.required' => 'Tavsif kiritilishi shart.',
            'description.min' => 'Tavsif kamida 5 ta belgidan iborat bo\'lishi kerak.',
            'description.max' => 'Tavsif 2000 ta belgidan oshmasligi kerak.',
            'photos.required' => "Kamida {$min} ta rasm yuklang.",
            'photos.array' => "Kamida {$min} ta rasm yuklang.",
            'photos.min' => "Kamida {$min} ta rasm yuklang.",
            'photos.max' => "Ko'pi bilan {$max} ta rasm yuklash mumkin.",
            'photos.*.required' => 'Rasm yuklanmadi, qaytadan urinib ko\'ring.',
            'photos.*.image' => 'Faqat rasm fayllarini yuklash mumkin.',
            'photos.*.mimes' => 'Rasm JPEG, PNG yoki WEBP formatida bo\'lishi kerak.',
            'photos.*.max' => 'Har bir rasm hajmi 5 MB dan oshmasligi kerak.',
            'photos.*.uploaded' => 'Rasm serverga yuklanmadi (hajmi juda katta bo\'lishi mumkin).',
        ]);

        $photoPaths = [];

        try {
            foreach ($request->file('photos', []) as $file) {
                $path = $file->store('uploads/treatment-logs', 'public');
                if (!$path) {
                    throw new \RuntimeException('Rasmni saqlab bo\'lmadi.');
                }
                $photoPaths[] = '/storage/' . $path;
            }

            $log = TreatmentLog::create([
                'doctor_id' => $validated['doctor_id'],
                'description' => $validated['description'],
                'photos' => $photoPaths,
            ]);
        } catch (\Throwable $e) {
            // Yarim yo'lda qolgan fayllar diskda "yetim" bo'lib qolmasin
            TreatmentLog::deleteStoredPhotos($photoPaths);
            throw $e;
        }

        return response()->json([
            'success' => true,
            'message' => 'Treatment log created successfully.',
            'data' => $log->load('doctor:id,full_name,slug'),
        ], 201);
    }

    public function destroy($id): JsonResponse
    {
        TreatmentLog::findOrFail($id)->deleteWithPhotos();

        return response()->json([
            'success' => true,
            'message' => 'Treatment log deleted successfully.',
        ]);
    }
}
