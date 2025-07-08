<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    public function index(): JsonResponse
    {
        $services = Service::all();
        return response()->json($services);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $service = Service::create($validator->validated());

        return response()->json($service, 201);
    }

    public function show(string $id): JsonResponse
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json(["message" => 'Servicio no encontrado'], 404);
        }

        return response()->json($service);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json(["message" => 'Servicio no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $service->update($validator->validated());

        return response()->json($service);
    }

    public function destroy(string $id): JsonResponse
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json(['message' => 'Servicio no encontrado'], 404);
        }

        $service->delete();

        return response()->json(['message' => 'Servicio eliminado con éxito']);
    }
}