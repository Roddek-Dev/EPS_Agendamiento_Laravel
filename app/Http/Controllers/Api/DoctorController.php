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
        $doctors = Doctor::all();
        return response()->json($doctors);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'specialty' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $doctor = Doctor::create($validator->validated());

        return response()->json($doctor, 201);
    }

    public function show(string $id)
    {
        $doctor = Doctor::find($id);

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

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'specialty' => 'nullable|string|max:255',
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