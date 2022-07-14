<?php

namespace Database\Seeders;

use App\Models\TypePeople;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypePeopleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types_peoples = [
            ['descripcion' => 'Medico'],
            ['descripcion' => 'Cliente'],
        ];

        TypePeople::truncate();
        foreach ($types_peoples as $type_people) {
            $type_people = (object) $type_people;
            TypePeople::create([
                'descripcion' => $type_people->descripcion,
                'created_usu' => 1,
                'updated_usu' => 1,
                'created_ip' => '127.0.0.1',
                'updated_ip' => '127.0.0.1',
            ]);
        }
    }
}
