<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class AppointmentController extends Controller
{
    public function index()
    {
        // Cargar citas con sus relaciones
        $appointments = Appointment::with(['patient:id,name', 'doctor:id,name', 'service:id,name'])->get();
        return response()->json($appointments);
    }

    public function store(Request $request)
    {
        // ✅ CAMBIO: Añadir 'service_id' a la validación
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'service_id' => 'nullable|exists:services,id', // Puede ser nulo, pero si existe, debe estar en la tabla services
            'appointment_time' => 'required|date_format:Y-m-d H:i:s', // Formato específico
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $appointment = Appointment::create($validator->validated());

        return response()->json($appointment, 201);
    }

    public function show(string $id)
    {
        // Cargar la cita con sus relaciones
        $appointment = Appointment::with(['patient:id,name', 'doctor:id,name', 'service:id,name'])->find($id);

        if (!$appointment) {
            return response()->json(["message" => 'Cita no encontrada'], 404);
        }

        return response()->json($appointment);
    }

    public function update(Request $request, string $id)
    {
        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json(["message" => 'Cita no encontrada'], 404);
        }

        // ✅ CAMBIO: Añadir 'service_id' a la validación de actualización
        $validator = Validator::make($request->all(), [
            'patient_id' => 'sometimes|exists:patients,id',
            'doctor_id' => 'sometimes|exists:doctors,id',
            'service_id' => 'nullable|exists:services,id',
            'appointment_time' => 'sometimes|date_format:Y-m-d H:i:s',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $appointment->update($validator->validated());

        return response()->json($appointment);
    }

    public function destroy(string $id)
    {
        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }

        $appointment->delete();

        return response()->json(['message' => 'Cita eliminada con éxito']);
    }

    public function listAppointmentsWithNames(): JsonResponse
    {
        $results = DB::select("
            SELECT
                a.id AS cita_id,
                a.appointment_time AS fecha_hora_cita,
                p.name AS nombre_paciente,
                d.name AS nombre_doctor,
                d.specialty AS especialidad_doctor,
            FROM
                appointments a
            JOIN
                patients p ON a.patient_id = p.id
            JOIN
                doctors d ON a.doctor_id = d.id
            ORDER BY
                a.appointment_time
        ");

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

        $doctorExists = DB::table('doctors')->where('id', $doctorId)->exists();
        if (!$doctorExists) {
             return response()->json(["message" => 'Doctor no encontrado'], 404);
        }

        $results = DB::select("
            SELECT
                a.id AS cita_id,
                a.appointment_time AS fecha_hora_cita,
                p.name AS nombre_paciente,
            FROM
                appointments a
            JOIN
                patients p ON a.patient_id = p.id
            WHERE
                a.doctor_id = ?
                AND a.appointment_time BETWEEN ? AND ?
            ORDER BY
                a.appointment_time
        ", [$doctorId, $startDate, $endDate]);

        return response()->json($results);
    }

    public function countAppointmentsPerDoctor(): JsonResponse
    {
        $results = DB::select("
            SELECT
                d.id AS doctor_id,
                d.name AS nombre_doctor,
                d.specialty AS especialidad,    
                COUNT(a.id) AS total_citas
            FROM
                doctors d
            LEFT JOIN
                appointments a ON d.id = a.doctor_id
            GROUP BY
                d.id, d.name, d.specialty
            ORDER BY
                total_citas DESC, nombre_doctor
        ");

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

        $specialty = $request->query('specialty');

        $results = DB::select("
            SELECT DISTINCT
                p.id AS paciente_id,
                p.name AS nombre_paciente,
                p.email AS email_paciente
            FROM
                patients p
            JOIN
                appointments a ON p.id = a.patient_id
            JOIN
                doctors d ON a.doctor_id = d.id
            WHERE
                d.specialty = ?
            ORDER BY
                p.name
        ", [$specialty]);

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

        $count = $request->query('count');
        $days = $request->query('days');

        $results = DB::select("
            SELECT
                d.id AS doctor_id,
                d.name AS nombre_doctor,
                COUNT(a.id) AS recent_appointments
            FROM
                doctors d
            LEFT JOIN
                appointments a ON d.id = a.doctor_id
                AND a.appointment_time >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            GROUP BY
                d.id, d.name
            HAVING
                COUNT(a.id) < ?
            ORDER BY
                recent_appointments ASC, d.name
        ", [$days, $count]);

        return response()->json($results);
    }
}