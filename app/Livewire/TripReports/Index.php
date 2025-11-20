<?php

namespace App\Livewire\TripReports;

use App\Models\TripReport;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }



    public function render()
    {
        $query = TripReport::with(['spt', 'createdByUser'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('doc_no', 'like', '%' . $this->search . '%')
                      ->orWhere('report_no', 'like', '%' . $this->search . '%')
                      ->orWhere('place_from', 'like', '%' . $this->search . '%')
                      ->orWhere('place_to', 'like', '%' . $this->search . '%')
                      ->orWhereHas('spt', function ($sptQuery) {
                          $sptQuery->where('doc_no', 'like', '%' . $this->search . '%');
                      });
                });
            })

            ->orderBy('created_at', 'desc');

        $tripReports = $query->paginate(10);

        return view('livewire.trip-reports.index', [
            'tripReports' => $tripReports,
        ]);
    }
}
