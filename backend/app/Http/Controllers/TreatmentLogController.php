<?php

namespace App\Http\Controllers;

use App\Models\TreatmentLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TreatmentLogController extends Controller
{
    // Admin: list all logs, optionally filtered by doctor
    public function index(Request $request): JsonResponse
    {
        $query = TreatmentLog::with('doctor')->latest();

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
            ->where('created_at', '>=', now()->subHours(24))
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'description' => 'required|string|min:5',
            'photos' => 'required|array|min:2',
            'photos.*' => 'image|mimes:jpeg,jpg,png,webp|max:5120',
        ]);

        $photoPaths = [];
        foreach ($request->file('photos', []) as $file) {
            $path = $file->store('uploads/treatment-logs', 'public');
            $photoPaths[] = '/storage/' . $path;
        }

        $log = TreatmentLog::create([
            'doctor_id' => $validated['doctor_id'],
            'description' => $validated['description'],
            'photos' => $photoPaths,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Treatment log created successfully.',
            'data' => $log->load('doctor'),
        ], 201);
    }

    public function destroy($id): JsonResponse
    {
        $log = TreatmentLog::findOrFail($id);

        foreach (($log->photos ?? []) as $photo) {
            if (str_starts_with($photo, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $photo));
            }
        }

        $log->delete();

        return response()->json([
            'success' => true,
            'message' => 'Treatment log deleted successfully.',
        ]);
    }
}