<?php

use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\SpecialtyController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\AuthController; // Importa el AuthController
// use App\Http\Controllers\Api\UsuarioController; // Descomentar si usas este controlador en apiResource

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckRole;


// Public authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (require Sanctum authentication token)
Route::middleware('auth:sanctum')->group(function () {

    // Authenticated user endpoints
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Rutas accesibles solo para administradores
    Route::middleware([CheckRole::class . ':admin'])->group(function () {
       
        
        // Rutas de reportes (solo admin)
        Route::get('/appointmentss/names', [ReportController::class, 'listAppointmentsWithNames']);
        Route::get('/doctorss/{doctorId}/appointments-in-range', [ReportController::class, 'getAppointmentsForDoctorInRange']);
        Route::get('/doctorss/appointment-counts', [ReportController::class, 'countAppointmentsPerDoctor']);
        Route::get('/patientss/by-doctor-specialty', [ReportController::class, 'listPatientsByDoctorSpecialty']);
        Route::get('/doctorss/less-than-recent-appointments', [ReportController::class, 'findDoctorsWithLessRecentAppointments']);
        Route::get('/servicess/{serviceId}/appointments', [ReportController::class, 'listAppointmentsByService']);
        Route::get('/specialtiess/doctor-counts', [ReportController::class, 'countDoctorsPerSpecialty']);
        Route::get('/specialtiess/services-used', [ReportController::class, 'listServicesBySpecialtyAppointments']);
        Route::get('/patientss/multiple-appointments-same-doctor', [ReportController::class, 'findPatientsWithMultipleAppointmentsWithSameDoctor']);
        Route::get('/appointmentss/future', [ReportController::class, 'listFutureAppointments']);
    });
     Route::apiResource('specialties', SpecialtyController::class);
        Route::apiResource('doctors', DoctorController::class);
        Route::apiResource('services', ServiceController::class);
    // Rutas accesibles para todos los usuarios autenticados
    Route::apiResource('patients', PatientController::class);
    Route::apiResource('appointments', AppointmentController::class);

    // Example if you have a users resource to protect
    // Route::apiResource('usuarios', UsuarioController::class)->except(['create', 'edit']);

});

// Optional: Other public routes if needed (uncommon for APIs)