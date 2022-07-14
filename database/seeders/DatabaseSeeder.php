<?php

namespace Database\Seeders;

use Database\Seeders\Auth\DatabaseSeeder as AuthDatabaseSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        $this->call([
            AuthDatabaseSeeder::class,
            TypePeopleSeeder::class,
            PeopleSeeder::class,
            PeopleHasPeopleTypeSeeder::class,
            PetTypeSeeder::class,
            PetSeeder::class,
            PetMedicalAppointmentSeeder::class,
        ]);
        Schema::enableForeignKeyConstraints();
    }
}
