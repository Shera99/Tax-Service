<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создание администратора
        User::updateOrCreate(
            ['email' => 'admin@taxservice.local'],
            [
                'name' => 'Администратор',
                'email' => 'admin@taxservice.local',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'email_verified_at' => now(),
            ]
        );

        // Создание тестового налоговика
        User::updateOrCreate(
            ['email' => 'tax@taxservice.local'],
            [
                'name' => 'Налоговик',
                'email' => 'tax@taxservice.local',
                'password' => Hash::make('password'),
                'role' => User::ROLE_TAX_OFFICER,
                'email_verified_at' => now(),
            ]
        );
    }
}
