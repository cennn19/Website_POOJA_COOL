<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@sejukabadi.com');
        $name = env('ADMIN_NAME', 'Admin SejukAbadi');
        $password = env('ADMIN_PASSWORD', 'password123');

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]
        );
    }
}
