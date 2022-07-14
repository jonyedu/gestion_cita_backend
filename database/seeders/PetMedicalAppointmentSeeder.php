<?php

namespace Database\Seeders;

use App\Models\Pet;
use App\Models\PetMedicalAppointment;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PetMedicalAppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $firulais = Pet::where('name', 'like', "%Firulais%")->first()->value('id');
        $porfidio = Pet::where('name', 'like', "%Porfidio%")->first()->value('id');
       
        $pet_medical_appointments = [
            ['pet_id' => $firulais, 'registration_date' => Carbon::now()->format('Y-m-d') , 'registration_time' => Carbon::now()->format('H:i:s'), 'turn' => 1],
            ['pet_id' => $porfidio, 'registration_date' => Carbon::now()->format('Y-m-d') , 'registration_time' => Carbon::now()->format('H:i:s'), 'turn' => 2],
        ];

        PetMedicalAppointment::truncate();
        foreach ($pet_medical_appointments as $pet_medical_appointment) {
            $pet_medical_appointment = (object) $pet_medical_appointment;
            PetMedicalAppointment::create([

                'pet_id' => $pet_medical_appointment->pet_id,
                'registration_date' => $pet_medical_appointment->registration_date,
                'registration_time' => $pet_medical_appointment->registration_time,
                'turn' => $pet_medical_appointment->turn,
                'created_usu' => 1,
                'updated_usu' => 1,
                'created_ip' => '127.0.0.1',
                'updated_ip' => '127.0.0.1',
            ]);
        }
    }
}
