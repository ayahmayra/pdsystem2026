<?php

namespace App\Console\Commands;

use App\Models\ApiClient;
use Illuminate\Console\Command;

class ListApiClients extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:list-clients';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all API clients';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $clients = ApiClient::all();

        if ($clients->isEmpty()) {
            $this->info('No API clients found.');
            return Command::SUCCESS;
        }

        $this->info('API Clients:');
        $this->newLine();

        $data = $clients->map(function ($client) {
            return [
                'ID' => $client->id,
                'Name' => $client->name,
                'Description' => $client->description ?: '-',
                'IP Whitelist' => $client->ip_whitelist ?: 'All IPs',
                'Status' => $client->is_active ? '✓ Active' : '✗ Inactive',
                'Requests' => number_format($client->request_count),
                'Last Used' => $client->last_used_at?->format('Y-m-d H:i:s') ?: 'Never',
                'Created' => $client->created_at->format('Y-m-d'),
            ];
        })->toArray();

        $this->table(
            ['ID', 'Name', 'Description', 'IP Whitelist', 'Status', 'Requests', 'Last Used', 'Created'],
            $data
        );

        return Command::SUCCESS;
    }
}
