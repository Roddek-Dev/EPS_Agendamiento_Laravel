<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\Appointment;
use App\Models\Service;

class ReportController extends Controller
{
    public function listAppointmentsWithNames(): JsonResponse
    {
        $appointments = Appointment::with([
            'patient:id,name',
            'doctor:id,name,specialty_id',
            'doctor.specialty:id,name'
        ])
            ->orderBy('appointment_time')
            ->get();

        $results = $appointments->map(function ($appointment) {
            return [
                'cita_id' => $appointment->id,
                'fecha_hora_cita' => $appointment->appointment_time,
                'nombre_paciente' => $appointment->patient->name ?? null,
                'nombre_doctor' => $appointment->doctor->name ?? null,
                'especialidad_doctor' => $appointment->doctor->specialty->name ?? null,
            ];
        });

        return response()->json($results);
    }

    public function getAppointmentsForDoctorInRange(string $doctorId, Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',           
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $doctor = Doctor::find($doctorId);
        if (!$doctor) {
            return response()->json(["message" => 'Doctor no encontrado'], 404);
        }

        $appointments = Appointment::with('patient:id,name')
            ->where('doctor_id', $doctorId)
            ->whereBetween('appointment_time', [$startDate . " 00:00:00", $endDate . " 23:59:59"])
            ->orderBy('appointment_time')
            ->get();

        $results = $appointments->map(function ($appointment) {
            return [
                'cita_id' => $appointment->id,
                'fecha_hora_cita' => $appointment->appointment_time,
                'nombre_paciente' => $appointment->patient->name ?? null,
            ];
        });

        return response()->json($results);
    }

    public function countAppointmentsPerDoctor(): JsonResponse
    {
        $doctors = Doctor::with('specialty:id,name')
            ->withCount('appointments')
            ->orderByDesc('appointments_count')
            ->orderBy('name')
            ->get();

        $results = $doctors->map(function ($doctor) {
            return [
                'doctor_id' => $doctor->id,
                'nombre_doctor' => $doctor->name,
                'especialidad' => $doctor->specialty->name ?? null,
                'total_citas' => $doctor->appointments_count,
            ];
        });

        return response()->json($results);
    }

    public function listPatientsByDoctorSpecialty(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'specialty' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $specialtyName = $request->query('specialty');

        $patients = Patient::select('id', 'name', 'email')
            ->whereHas('appointments.doctor.specialty', function ($query) use ($specialtyName) {
                $query->where('name', $specialtyName);
            })
            ->distinct()
            ->orderBy('name')
            ->get();

        $results = $patients->map(function ($patient) {
            return [
                'paciente_id' => $patient->id,
                'nombre_paciente' => $patient->name,
                'email_paciente' => $patient->email,
            ];
        });

        return response()->json($results);
    }

    public function findDoctorsWithLessRecentAppointments(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'count' => 'required|integer|min:0',
            'days' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $thresholdCount = $request->query('count');
        $numberOfDays = $request->query('days');
        $dateLimit = now()->subDays($numberOfDays)->toDateString();

        $doctors = Doctor::select('id', 'name')
            ->withCount(['appointments' => function ($query) use ($dateLimit) {
                $query->where('appointment_time', '>=', $dateLimit . " 00:00:00");
            }])
            ->having('appointments_count', '<', $thresholdCount)
            ->orderBy('appointments_count')
            ->orderBy('name')
            ->get();

        $results = $doctors->map(function ($doctor) {
            return [
                'doctor_id' => $doctor->id,
                'nombre_doctor' => $doctor->name,
                'recent_appointments' => $doctor->appointments_count,
            ];
        });

        return response()->json($results);
    }

     public function listAppointmentsByService(string $serviceId): JsonResponse
     {
         $service = Service::find($serviceId); 
 
         if (!$service) {
             return response()->json(["message" => 'Servicio no encontrado'], 404);
         }
 
         $appointments = Appointment::with(['patient:id,name', 'doctor:id,name'])
             ->where('service_id', $serviceId)
             ->orderBy('appointment_time')
             ->get();
 
         $results = $appointments->map(function ($appointment) use ($service) {
             return [
                 'cita_id' => $appointment->id,
                 'fecha_hora_cita' => $appointment->appointment_time,
                 'nombre_paciente' => $appointment->patient->name ?? null,
                 'nombre_doctor' => $appointment->doctor->name ?? null,
                 'servicio' => $service->name, 
             ];
         });
 
         return response()->json($results);
     }
 
     public function countDoctorsPerSpecialty(): JsonResponse
     {
         $specialtiesWithDoctorCount = Specialty::withCount('doctors')
             ->orderByDesc('doctors_count')
             ->orderBy('name')
             ->get();
 
         $results = $specialtiesWithDoctorCount->map(function ($specialty) {
             return [
                 'especialidad_id' => $specialty->id,
                 'nombre_especialidad' => $specialty->name,
                 'total_doctores' => $specialty->doctors_count,
             ];
         });
 
         return response()->json($results);
     }
 
     public function listServicesBySpecialtyAppointments(Request $request): JsonResponse
     {
         $validator = Validator::make($request->all(), [
             'specialty' => 'required|string|max:255',
         ]);
 
         if ($validator->fails()) {
             return response()->json($validator->errors(), 422);
         }
 
         $specialtyName = $request->query('specialty');
 
         $services = Service::whereHas('appointments.doctor.specialty', function ($query) use ($specialtyName) {
             $query->where('name', $specialtyName);
         })
         ->distinct() 
         ->orderBy('name')
         ->get(['id', 'name', 'description']); 
 
         $results = $services->map(function ($service) {
             return [
                 'servicio_id' => $service->id,
                 'nombre_servicio' => $service->name,
                 'descripcion_servicio' => $service->description,
             ];
         });
 
         return response()->json($results);
     }
 
     public function findPatientsWithMultipleAppointmentsWithSameDoctor(): JsonResponse
     {
         $multipleAppointments = Appointment::select('patient_id', 'doctor_id')
             ->groupBy('patient_id', 'doctor_id')
             ->havingRaw('COUNT(*) > 1') 
             ->get();
 
         $results = [];
 
         foreach ($multipleAppointments as $appointmentGroup) {
             $patient = Patient::find($appointmentGroup->patient_id, ['id', 'name']);
             $doctor = Doctor::find($appointmentGroup->doctor_id, ['id', 'name']);
 
             $count = Appointment::where('patient_id', $appointmentGroup->patient_id)
                                  ->where('doctor_id', $appointmentGroup->doctor_id)
                                  ->count();
 
 
             if ($patient && $doctor) {
                  $results[] = [
                     'paciente_id' => $patient->id,
                     'nombre_paciente' => $patient->name,
                     'doctor_id' => $doctor->id,
                     'nombre_doctor' => $doctor->name,
                     'numero_citas_con_este_doctor' => $count,
                  ];
             }
         }
 

         $formattedResults = collect($results)->unique(function ($item) {
             return $item['paciente_id'] . '-' . $item['doctor_id'];
         })->values()->all();
 
 
         return response()->json($formattedResults);
     }
 
 
     public function listFutureAppointments(): JsonResponse
     {
         $appointments = Appointment::with([
             'patient:id,name',
             'doctor:id,name,specialty_id',
             'doctor.specialty:id,name'
         ])
             ->where('appointment_time', '>', now()) // Filtra por citas en el futuro
             ->orderBy('appointment_time')
             ->get();
 
         $results = $appointments->map(function ($appointment) {
             return [
                 'cita_id' => $appointment->id,
                 'fecha_hora_cita' => $appointment->appointment_time,
                 'nombre_paciente' => $appointment->patient->name ?? null,
                 'nombre_doctor' => $appointment->doctor->name ?? null,
                 'especialidad_doctor' => $appointment->doctor->specialty->name ?? null,
             ];
         });
 
         return response()->json($results);
     }
}