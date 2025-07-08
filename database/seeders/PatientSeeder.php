<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Inserta algunos registros de pacientes
        DB::table('patients')->insert([
            [
                'name' => 'Ana Maria Gomez',
                'email' => 'ana.gomez@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Carlos Lopez',
                'email' => 'carlos.l@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
             [
                'name' => 'Maria Fernanda Diaz',
                'email' => 'mf.diaz@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
             [
                'name' => 'Pedro Ramirez',
                'email' => 'pedro.r@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}