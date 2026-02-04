<?php

namespace Database\Seeders;

use App\Models\ApiKey;
use Illuminate\Database\Seeder;

class ApiKeySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создание тестового API ключа
        ApiKey::updateOrCreate(
            ['name' => 'Test Service'],
            [
                'name' => 'Test Service',
                'public_key' => 'pub_test_1234567890abcdef12345678',
                'secret_key' => 'sec_test_abcdef1234567890abcdef12',
                'is_active' => true,
            ]
        );
    }
}
