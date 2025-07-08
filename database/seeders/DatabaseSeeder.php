<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Llama a los seeders en el orden correcto:
        // Tablas sin dependencias de otras tablas nuestras van primero.
        // Tablas con dependencias van después de sus dependencias.
        $this->call([
            SpecialtySeeder::class, // Especialidades no dependen de otras tablas nuestras
            ServiceSeeder::class,   // Servicios no dependen de otras tablas nuestras
            PatientSeeder::class,   // Pacientes no dependen de otras tablas nuestras
            DoctorSeeder::class,    // Doctores dependen de Especialidades (ya llamada arriba)
            AppointmentSeeder::class, // Citas dependen de Pacientes, Doctores y Servicios (ya llamadas arriba)
        ]);

        // Si también tienes el UserSeeder por defecto y quieres ejecutarlo, descomenta:
        // $this->call(UserSeeder::class);
    }
}