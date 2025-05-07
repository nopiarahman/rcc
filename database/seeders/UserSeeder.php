<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'nopiarahman2@gmail.com'],
            [
                'name' => 'Nopi Arahman',
                'email' => 'nopiarahman2@gmail.com',
                'password' => Hash::make('123Jajan!')
            ]
        );
    }
}