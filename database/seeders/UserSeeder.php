<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Membuat satu akun admin awal dari .env (ADMIN_EMAIL, ADMIN_NAME, ADMIN_PASSWORD).
     * Password TIDAK boleh ditulis di kode. Akun yang sudah ada tidak diubah.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@example.com');
        $password = env('ADMIN_PASSWORD') ?: Str::password(16);

        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => env('ADMIN_NAME', 'Admin'), 'password' => $password]
        );

        if ($user->wasRecentlyCreated && ! env('ADMIN_PASSWORD')) {
            $this->command?->warn("Admin {$email} dibuat dengan password acak: {$password}");
            $this->command?->warn('Segera ganti lewat halaman Data Admin atau Settings > Password.');
        }
    }
}
