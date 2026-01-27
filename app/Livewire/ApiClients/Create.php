<?php

namespace App\Livewire\ApiClients;

use App\Models\ApiClient;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', ['title' => 'Tambah API Client'])]
class Create extends Component
{
    public $name = '';
    public $description = '';
    public $ip_whitelist = '';
    public $is_active = true;
    public $generatedApiKey = null;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'ip_whitelist' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ];
    }

    public function save()
    {
        $validated = $this->validate();
        
        // Generate API key
        $apiKey = ApiClient::generateApiKey();
        
        // Create client
        $client = ApiClient::create([
            'name' => $validated['name'],
            'api_key' => $apiKey, // Will be hashed automatically
            'description' => $validated['description'] ?? null,
            'ip_whitelist' => !empty($validated['ip_whitelist']) ? $validated['ip_whitelist'] : null,
            'is_active' => $validated['is_active'] ?? true,
        ]);
        
        // Store generated API key in session to show in view
        $this->generatedApiKey = $apiKey;
        
        session()->flash('message', 'API client berhasil dibuat.');
        session()->flash('api_key', $apiKey); // Store in session for display
        
        // Reset form
        $this->reset(['name', 'description', 'ip_whitelist', 'is_active']);
        
        // Don't redirect, show API key modal instead
    }

    public function closeModal()
    {
        $this->generatedApiKey = null;
        session()->forget('api_key');
        return redirect()->route('api-clients.index');
    }

    public function render()
    {
        return view('livewire.api-clients.create');
    }
}
