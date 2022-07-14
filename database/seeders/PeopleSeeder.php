<?php

namespace Database\Seeders;

use App\Models\People;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PeopleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $peoples = [
            ['name' => 'Juan', 'identification_card' => '0952557496', 'phone' => '0994848358', 'direction' => 'duran'],
            ['name' => 'Pedro', 'identification_card' => '0584365849', 'phone' => '0994949832', 'direction' => 'guayaquil'],
        ];

        People::truncate();
        foreach ($peoples as $people) {
            $people = (object) $people;
            People::create([
                'name' => $people->name,
                'identification_card' => $people->identification_card,
                'phone' => $people->phone,
                'direction' => $people->direction,
                'created_usu' => 1,
                'updated_usu' => 1,
                'created_ip' => '127.0.0.1',
                'updated_ip' => '127.0.0.1',
            ]);
        }
    }
}
