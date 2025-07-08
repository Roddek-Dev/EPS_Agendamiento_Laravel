<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Importa el Facade DB
use Illuminate\Support\Str; // Importa Str si necesitas UUIDs, aunque con IDs auto-incrementales no es necesario aquí

class SpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Inserta algunos registros de especialidades
        DB::table('specialties')->insert([
            [
                'name' => 'Cardiología',
                'description' => 'Diagnóstico y tratamiento de enfermedades del corazón.',
                'created_at' => now(), // Utiliza la función now() de Laravel/Carbon
                'updated_at' => now(),
            ],
            [
                'name' => 'Pediatría',
                'description' => 'Atención médica de niños y adolescentes.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dermatología',
                'description' => 'Enfermedades de la piel.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}