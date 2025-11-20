<?php

namespace App\Livewire\DistrictPerdiemRates;

use App\Models\DistrictPerdiemRate;
use App\Helpers\PermissionHelper;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $districtFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'districtFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingDistrictFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = DistrictPerdiemRate::with(['district', 'travelGrade'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('district', function ($districtQuery) {
                          $districtQuery->where('name', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('travelGrade', function ($gradeQuery) {
                          $gradeQuery->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->districtFilter, function ($query) {
                $query->where('district_id', $this->districtFilter);
            })
            ->orderBy('district_id')
            ->orderBy('travel_grade_id')
            ->orderBy('perdiem_rate', 'desc');

        $districtPerdiemRates = $query->paginate(10);

        $districts = \App\Models\District::orderBy('name')->get();

        return view('livewire.district-perdiem-rates.index', [
            'districtPerdiemRates' => $districtPerdiemRates,
            'districts' => $districts,
        ]);
    }
}
