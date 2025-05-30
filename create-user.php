<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Check if user already exists
$existingUser = User::where('email', 'ridhosaputra194@gmail.com')->first();

if ($existingUser) {
    echo "User with email ridhosaputra194@gmail.com already exists.\n";
} else {
    // Create new user
    $user = new User();
    $user->name = 'Ridho Saputra';
    $user->email = 'ridhosaputra194@gmail.com';
    $user->password = Hash::make('hanumcoffee');
    $user->save();
    
    echo "User created successfully with email: ridhosaputra194@gmail.com\n";
}
