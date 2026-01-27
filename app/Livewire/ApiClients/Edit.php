<?php

namespace App\Livewire\ApiClients;

use App\Models\ApiClient;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', ['title' => 'Edit API Client'])]
class Edit extends Component
{
    public ApiClient $apiClient;
    public $name = '';
    public $description = '';
    public $ip_whitelist = '';
    public $is_active = true;

    public function mount(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
        $this->name = $apiClient->name;
        $this->description = $apiClient->description ?? '';
        $this->ip_whitelist = $apiClient->ip_whitelist ?? '';
        $this->is_active = $apiClient->is_active;
    }

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
        
        $this->apiClient->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'ip_whitelist' => !empty($validated['ip_whitelist']) ? $validated['ip_whitelist'] : null,
            'is_active' => $validated['is_active'] ?? true,
        ]);
        
        session()->flash('message', 'API client berhasil diperbarui.');
        
        return redirect()->route('api-clients.index');
    }

    public function render()
    {
        return view('livewire.api-clients.edit');
    }
}
