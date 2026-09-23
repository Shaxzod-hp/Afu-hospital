<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\NewsCategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SpecializationController;
use App\Http\Controllers\StatsionarPackageController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\SurgeryController;
use App\Http\Controllers\TreatmentLogController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public Auth routes
Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:login');

// Authenticated User routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
});

// Admin-only settings routes (auth:sanctum + admin required)
Route::middleware(['auth:sanctum', 'abilities:admin', 'admin'])->group(function () {
    Route::put('/admin/settings/password', [AuthController::class, 'changePassword']);
    Route::put('/admin/profile', [AuthController::class, 'updateProfile']);
});

// Admin protected module routes
Route::middleware(['auth:sanctum', 'abilities:admin', 'admin'])->prefix('admin')->group(function () {
    Route::apiResource('specializations', SpecializationController::class)->scoped(['specialization' => 'id']);
    Route::apiResource('doctors', DoctorController::class)->scoped(['doctor' => 'id']);
    Route::apiResource('services', ServiceController::class)->scoped(['service' => 'id']);
    Route::apiResource('statsionar', StatsionarPackageController::class)->scoped(['statsionar' => 'id']);
    Route::apiResource('news', NewsController::class)->scoped(['news' => 'id']);
    Route::apiResource('surgeries', SurgeryController::class)->scoped(['surgery' => 'id']);
    Route::apiResource('contacts', ContactController::class)->except(['store', 'show']);

    Route::get('news-categories', [NewsCategoryController::class, 'index']);
    Route::post('news-categories', [NewsCategoryController::class, 'store']);

    Route::get('treatment-logs', [TreatmentLogController::class, 'index']);
    Route::post('treatment-logs', [TreatmentLogController::class, 'store']);
    Route::delete('treatment-logs/{id}', [TreatmentLogController::class, 'destroy']);
});

// Public services routes
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{slug}', [ServiceController::class, 'show']);

// Public specializations routes
Route::get('/specializations', [SpecializationController::class, 'index']);

// Public statsionar routes
Route::get('/statsionar', [StatsionarPackageController::class, 'index']);
Route::get('/statsionar/{slug}', [StatsionarPackageController::class, 'show']);

// Public News routes
Route::get('/news', [NewsController::class, 'index']);
Route::get('/news/{slug}', [NewsController::class, 'show']);

// Public Doctor routes
Route::get('/doctors', [DoctorController::class, 'index']);
Route::get('/doctors/{idOrSlug}', [DoctorController::class, 'show']);

// Public Surgery routes
Route::get('/surgeries', [SurgeryController::class, 'index']);
Route::get('/surgeries/{slug}', [SurgeryController::class, 'show']);

// Public Treatment Log routes (today's active entries per doctor slug/slug)
Route::get('/doctors/{slug}/treatment-logs', [TreatmentLogController::class, 'forDoctor']);

// Public Contacts routes
Route::post('/contacts', [ContactController::class, 'store']);