<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Patient; // Importa modelos para obtener IDs
use App\Models\Doctor;
use App\Models\Service;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // *** Importante: Asegúrate de que PatientSeeder, DoctorSeeder y ServiceSeeder se ejecutan ANTES ***
        // Obtiene IDs de los seeders anteriores
        $patientAnaId = Patient::where('email', 'ana.gomez@example.com')->first()?->id;
        $patientCarlosId = Patient::where('email', 'carlos.l@example.com')->first()?->id;
        $patientMariaFId = Patient::where('email', 'mf.diaz@example.com')->first()?->id;

        $doctorJuanId = Doctor::where('name', 'Dr. Juan Rodriguez')->first()?->id;
        $doctorLauraId = Doctor::where('name', 'Dra. Laura Perez')->first()?->id;
        $doctorAndresId = Doctor::where('name', 'Dr. Andres Salazar')->first()?->id;

        $servicioConsultaId = Service::where('name', 'Consulta General')->first()?->id;
        $servicioPrenatalId = Service::where('name', 'Control Prenatal')->first()?->id;
        $servicioVacunacionId = Service::where('name', 'Vacunación Infantil')->first()?->id;


        // Inserta algunos registros de citas
        DB::table('appointments')->insert([
            [
                'patient_id' => $patientAnaId,
                'doctor_id' => $doctorJuanId,
                'service_id' => $servicioConsultaId,
                'appointment_time' => now()->addDays(5)->setTime(10, 0, 0), // Cita en 5 días a las 10:00
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'patient_id' => $patientCarlosId,
                'doctor_id' => $doctorLauraId,
                 'service_id' => $servicioPrenatalId,
                'appointment_time' => now()->addDays(7)->setTime(14, 30, 0), // Cita en 7 días a las 14:30
                'created_at' => now(),
                'updated_at' => now(),
            ],
             [
                'patient_id' => $patientMariaFId,
                'doctor_id' => $doctorJuanId,
                'service_id' => $servicioConsultaId,
                'appointment_time' => now()->addDays(10)->setTime(9, 0, 0), // Cita en 10 días a las 9:00
                'created_at' => now(),
                'updated_at' => now(),
            ],
             [
                'patient_id' => $patientCarlosId,
                'doctor_id' => $doctorAndresId,
                'service_id' => $servicioConsultaId, // Consulta General con dermatologo
                'appointment_time' => now()->addDays(12)->setTime(11, 0, 0), // Cita en 12 días a las 11:00
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}