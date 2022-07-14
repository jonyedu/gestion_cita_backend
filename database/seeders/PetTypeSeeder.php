<?php

namespace Database\Seeders;

use App\Models\PetType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PetTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pet_types = [
            ['descripcion' => 'Canes', ],
            ['descripcion' => 'Felinos', ],
            ['descripcion' => 'Aves', ],
        ];

        PetType::truncate();
        foreach ($pet_types as $pet_type) {
            $pet_type = (object) $pet_type;
            PetType::create([
                'descripcion' => $pet_type->descripcion,
                'created_usu' => 1,
                'updated_usu' => 1,
                'created_ip' => '127.0.0.1',
                'updated_ip' => '127.0.0.1',
            ]);
        }
    }
}
