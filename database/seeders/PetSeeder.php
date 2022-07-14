<?php

namespace Database\Seeders;

use App\Models\People;
use App\Models\Pet;
use App\Models\PetType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $juan = People::where('name', 'like', "%juan%")->first()->value('id');
        $canes = PetType::where('descripcion', 'like', "%canes%")->first()->value('id');


        $Pedro = People::where('name', 'like', "%Pedro%")->first()->value('id');
        $felinos = PetType::where('descripcion', 'like', "%felinos%")->first()->value('id');

        $pets = [
            ['people_id' => $juan, 'pet_type_id' => $canes, 'name' => 'Firulais', 'age' => 2],
            ['people_id' => $Pedro, 'pet_type_id' => $felinos, 'name' => 'Porfidio', 'age' => 1],
        ];

        Pet::truncate();
        foreach ($pets as $pet) {
            $pet = (object) $pet;
            Pet::create([
                'people_id' => $pet->people_id,
                'pet_type_id' => $pet->pet_type_id,
                'name' => $pet->name,
                'age' => $pet->age,
                'created_usu' => 1,
                'updated_usu' => 1,
                'created_ip' => '127.0.0.1',
                'updated_ip' => '127.0.0.1',
            ]);
        }
    }
}
