<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PatientController extends Controller
{
    public function index()
    {
        $patients = Patient::all();
        return response()->json($patients);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:patients,email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $patient = Patient::create($validator->validated());

        return response()->json($patient, 201);
    }

    public function show(string $id)
    {
        $patient = Patient::find($id);

        if (!$patient) {
            return response()->json(["message" => 'Paciente no encontrado'], 404);
        }

        return response()->json($patient);
    }

    public function update(Request $request, string $id)
    {
        $patient = Patient::find($id);

        if (!$patient) {
            return response()->json(["message" => 'Paciente no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => 'nullable|email|unique:patients,email,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $patient->update($validator->validated());

        return response()->json($patient);
    }

    public function destroy(string $id)
    {
        $patient = Patient::find($id);

        if (!$patient) {
            return response()->json(['message' => 'Paciente no encontrado'], 404);
        }

        $patient->delete();

        return response()->json(['message' => 'Paciente eliminado con exito']);
    }
}