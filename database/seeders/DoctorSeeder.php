<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Specialty; // Importa el modelo Specialty para obtener IDs

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cardioId = Specialty::where('name', 'Cardiología')->first()?->id;
        $pediatriaId = Specialty::where('name', 'Pediatría')->first()?->id;
        $dermatoId = Specialty::where('name', 'Dermatología')->first()?->id;


        // Inserta algunos registros de doctores
        DB::table('doctors')->insert([
            [
                'name' => 'Dr. Juan Rodriguez',
                'specialty_id' => $cardioId, // Asigna el ID de Cardiología
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dra. Laura Perez',
                'specialty_id' => $pediatriaId, // Asigna el ID de Pediatría
                'created_at' => now(),
                'updated_at' => now(),
            ],
             [
                'name' => 'Dr. Andres Salazar',
                'specialty_id' => $dermatoId, // Asigna el ID de Dermatología
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}