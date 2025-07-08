<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Inserta algunos registros de servicios
        DB::table('services')->insert([
            [
                'name' => 'Consulta General',
                'description' => 'Consulta de atención primaria.',
                'price' => 50.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Control Prenatal',
                'description' => 'Seguimiento de embarazo.',
                'price' => 80.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
             [
                'name' => 'Vacunación Infantil',
                'description' => 'Aplicación de vacunas en niños.',
                'price' => 30.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}