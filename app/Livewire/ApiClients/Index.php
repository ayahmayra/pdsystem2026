<?php

namespace App\Livewire\ApiClients;

use App\Models\ApiClient;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app', ['title' => 'Manajemen API Key'])]
class Index extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleStatus(ApiClient $apiClient)
    {
        try {
            $apiClient->update([
                'is_active' => !$apiClient->is_active
            ]);
            
            $status = $apiClient->is_active ? 'diaktifkan' : 'dinonaktifkan';
            session()->flash('message', "API client berhasil {$status}.");
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengubah status API client. ' . $e->getMessage());
        }
    }

    public function delete(ApiClient $apiClient)
    {
        try {
            $apiClient->delete();
            session()->flash('message', 'API client berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus API client. ' . $e->getMessage());
        }
    }

    public function render()
    {
        $clients = ApiClient::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.api-clients.index', compact('clients'));
    }
}
