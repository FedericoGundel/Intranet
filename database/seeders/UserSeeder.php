<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'fede.gundel@gmail.com'],
            [
                'name' => 'Federico Gundel',
                'password' => Hash::make('12345678'),
                'approved' => 1,
            ]
        );
    }
}
