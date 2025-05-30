<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // First user
        User::updateOrCreate(
            ['email' => 'nopiarahman2@gmail.com'],
            [
                'name' => 'Nopi Arahman',
                'email' => 'nopiarahman2@gmail.com',
                'password' => Hash::make('123Jajan!')
            ]
        );
        
        // Second user
        User::updateOrCreate(
            ['email' => 'ridhosaputra194@gmail.com'],
            [
                'name' => 'Ridho Saputra',
                'email' => 'ridhosaputra194@gmail.com',
                'password' => Hash::make('hanumcoffee')
            ]
        );
    }
}