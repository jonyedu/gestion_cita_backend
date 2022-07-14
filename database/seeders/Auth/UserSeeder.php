<?php

namespace Database\Seeders\Auth;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = public_path() . '/images/admin.jpg';
        $base64 = convertImgToBase64($path);
        // $photo = convertBase64ToBinary($base64);
        User::truncate();
        User::create([
            'name' => 'Jonathan Eduardo Mora Candelario',
            'email' =>  'jonathan_1308@hotmail.com',
            'user_name' =>  'jonyedu19',
            'profile_photo_path' =>  $base64,
            'password' => Hash::make('jony,.123'),
        ]);
    }
}
