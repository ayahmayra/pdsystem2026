<?php

namespace App\Console\Commands;

use App\Models\ApiClient;
use Illuminate\Console\Command;

class CreateApiClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:create-client 
                            {name : Nama aplikasi/client}
                            {--description= : Deskripsi aplikasi}
                            {--ip= : IP whitelist (comma-separated)}
                            {--active : Set client sebagai aktif}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new API client with API key';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->argument('name');
        $description = $this->option('description') ?? '';
        $ipWhitelist = $this->option('ip');
        $isActive = $this->option('active') ?? true;

        // Generate API key
        $apiKey = ApiClient::generateApiKey();

        // Create client
        $client = ApiClient::create([
            'name' => $name,
            'api_key' => $apiKey, // Will be hashed automatically
            'description' => $description,
            'ip_whitelist' => $ipWhitelist,
            'is_active' => $isActive,
        ]);

        $this->info('API Client created successfully!');
        $this->newLine();
        $this->table(
            ['Field', 'Value'],
            [
                ['ID', $client->id],
                ['Name', $client->name],
                ['Description', $client->description ?: '-'],
                ['IP Whitelist', $client->ip_whitelist ?: 'All IPs'],
                ['Status', $client->is_active ? 'Active' : 'Inactive'],
            ]
        );
        
        $this->newLine();
        $this->warn('API Key (save this securely, it won\'t be shown again):');
        $this->line($apiKey);
        $this->newLine();
        $this->info('Use this API key in your requests:');
        $this->line('Header: X-API-Key: ' . $apiKey);
        $this->line('Or Query: ?api_key=' . $apiKey);

        return Command::SUCCESS;
    }
}
