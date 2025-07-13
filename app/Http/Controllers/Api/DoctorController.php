<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DoctorController extends Controller
{
    public function index()
    {
        // Obtener doctores con su especialidad cargada
        $doctors = Doctor::with('specialty:id,name')->get();
        return response()->json($doctors);
    }

    public function store(Request $request)
    {
        // ✅ CAMBIO: Validar 'specialty_id' en lugar de 'specialty'
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'specialty_id' => 'required|exists:specialties,id', // Asegura que el ID de la especialidad exista
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $doctor = Doctor::create($validator->validated());

        return response()->json($doctor, 201);
    }

    public function show(string $id)
    {
        // Cargar el doctor con su especialidad
        $doctor = Doctor::with('specialty:id,name')->find($id);

        if (!$doctor) {
            return response()->json(["message" => 'Doctor no encontrado'], 404);
        }

        return response()->json($doctor);
    }

    public function update(Request $request, string $id)
    {
        $doctor = Doctor::find($id);

        if (!$doctor) {
            return response()->json(["message" => 'Doctor no encontrado'], 404);
        }

        // ✅ CAMBIO: Validar 'specialty_id' en lugar de 'specialty'
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255', // 'sometimes' para que sea opcional en la actualización
            'specialty_id' => 'sometimes|exists:specialties,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $doctor->update($validator->validated());

        return response()->json($doctor);
    }

    public function destroy(string $id)
    {
        $doctor = Doctor::find($id);

        if (!$doctor) {
            return response()->json(['message' => 'Doctor no encontrado'], 404);
        }

        $doctor->delete();

        return response()->json(['message' => 'Doctor eliminado con exito']);
    }
}