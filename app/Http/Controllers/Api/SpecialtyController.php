<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class SpecialtyController extends Controller
{
    public function index(): JsonResponse
    {
        $specialties = Specialty::all();
        return response()->json($specialties);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:specialties,name|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $specialty = Specialty::create($validator->validated());

        return response()->json($specialty, 201);
    }

    public function show(string $id): JsonResponse
    {
        $specialty = Specialty::find($id);

        if (!$specialty) {
            return response()->json(["message" => 'Especialidad no encontrada'], 404);
        }

        return response()->json($specialty);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $specialty = Specialty::find($id);

        if (!$specialty) {
            return response()->json(["message" => 'Especialidad no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|unique:specialties,name,' . $id . '|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $specialty->update($validator->validated());

        return response()->json($specialty);
    }

    public function destroy(string $id): JsonResponse
    {
        $specialty = Specialty::find($id);

        if (!$specialty) {
            return response()->json(['message' => 'Especialidad no encontrada'], 404);
        }

        $specialty->delete();

        return response()->json(['message' => 'Especialidad eliminada con éxito']);
    }
}