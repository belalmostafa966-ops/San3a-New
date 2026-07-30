<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['phone_number' => '01000000000'],
            [
                'name' => 'System Admin',
                'role' => 'admin',
                'is_active' => true,
                'phone' => '01000000000', 
                'password' => Hash::make('12345678'),
            ]
        );
    }
}