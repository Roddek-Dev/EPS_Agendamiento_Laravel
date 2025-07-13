<?php

use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\SpecialtyController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas de API
|--------------------------------------------------------------------------
*/

// --- Rutas Públicas (No requieren autenticación) ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// --- Rutas Protegidas (Requieren token de autenticación) ---
Route::middleware('auth:sanctum')->group(function () {
    
    // Autenticación
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // --- CRUDs (Accesibles para cualquier usuario logueado) ---
    Route::apiResource('patients', PatientController::class);
    Route::apiResource('appointments', AppointmentController::class);
    Route::apiResource('doctors', DoctorController::class);
    Route::apiResource('services', ServiceController::class);
    Route::apiResource('specialties', SpecialtyController::class);

    // --- Reportes (También serán accesibles para todos los usuarios logueados) ---
    // He mantenido tus rutas de reportes aquí. Si en el futuro solo quieres que
    // los admins las vean, simplemente las mueves dentro de un nuevo grupo de middleware.
    Route::get('/reports/appointments-with-names', [ReportController::class, 'listAppointmentsWithNames']);
    Route::get('/reports/doctor-appointments-range/{doctorId}', [ReportController::class, 'getAppointmentsForDoctorInRange']);
    // ... puedes añadir el resto de tus rutas de reportes aquí...

});