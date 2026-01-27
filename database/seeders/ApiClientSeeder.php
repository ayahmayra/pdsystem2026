<?php

namespace Database\Seeders;

use App\Models\ApiClient;
use Illuminate\Database\Seeder;

class ApiClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generate API key untuk contoh
        $apiKey = ApiClient::generateApiKey();

        ApiClient::create([
            'name' => 'Aplikasi Referensi Internal',
            'api_key' => $apiKey, // Will be hashed automatically
            'description' => 'Aplikasi internal untuk mengakses data referensi',
            'ip_whitelist' => null, // Allow all IPs
            'is_active' => true,
        ]);

        // Output API key untuk dokumentasi (hanya di development)
        if (app()->environment('local')) {
            $this->command->info('API Client created!');
            $this->command->warn('API Key (save this, it won\'t be shown again): ' . $apiKey);
            $this->command->info('Store this API key securely. It is used to authenticate API requests.');
        }
    }
}
