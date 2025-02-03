<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::insert([
            ['username' => 'superadmin', 'email' => 'superadmin@example.com', 'password' => Hash::make('superadmin123'), 'role' => 'super_admin'],
            ['username' => 'admin_venue_1', 'email' => 'adminvenue1@example.com', 'password' => Hash::make('admin123'), 'role' => 'admin_venue'],
            ['username' => 'admin_venue_2', 'email' => 'adminvenue2@example.com', 'password' => Hash::make('admin123'), 'role' => 'admin_venue'],
            ['username' => 'infobar_1', 'email' => 'infobar1@example.com', 'password' => Hash::make('infobar123'), 'role' => 'infobar'],
            ['username' => 'infobar_2', 'email' => 'infobar2@example.com', 'password' => Hash::make('infobar123'), 'role' => 'infobar'],
        ]);
    }
}
