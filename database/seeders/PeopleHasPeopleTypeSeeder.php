<?php

namespace Database\Seeders;

use App\Models\People;
use App\Models\TypePeople;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PeopleHasPeopleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Cliente
        $type_peoples = TypePeople::select('id', 'descripcion')
            ->where('descripcion', 'like', "cliente")
            ->get();
        $people = People::where('name', "Juan")->first();
        $people->typePeoples()->sync($type_peoples->pluck('id'));

        //Medico
        $type_peoples = TypePeople::select('id', 'descripcion')
            ->where('descripcion', 'like', "Medico")
            ->get();
        $people = People::where('name', "Pedro")->first();
        $people->typePeoples()->sync($type_peoples->pluck('id'));
    }
}
