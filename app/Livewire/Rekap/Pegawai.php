<?php

namespace App\Livewire\Rekap;

use App\Models\User;
use App\Models\Unit;
use App\Models\Position;
use App\Models\Rank;
use App\Models\Spt;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class Pegawai extends Component
{
    use WithPagination;

    public $search = '';
    public $unit_filter = '';
    public $position_filter = '';
    public $rank_filter = '';
    public $selected_month = '';
    public $selected_year = '';
    public $perPage = 10;

    public function mount()
    {
        $this->selected_month = now()->format('m');
        $this->selected_year = now()->format('Y');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedUnitFilter()
    {
        $this->resetPage();
    }

    public function updatedPositionFilter()
    {
        $this->resetPage();
    }

    public function updatedRankFilter()
    {
        $this->resetPage();
    }

    public function updatedSelectedMonth()
    {
        $this->resetPage();
    }

    public function updatedSelectedYear()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = User::with([
            'unit',
            'position.echelon',
            'rank',
            'travelGrade'
        ])->where('users.is_non_staff', false); // Exclude non-staff users

        // Apply unit scope filtering for bendahara pengeluaran pembantu
        if (!\App\Helpers\PermissionHelper::canAccessAllData()) {
            $userUnitId = \App\Helpers\PermissionHelper::getUserUnitId();
            if ($userUnitId) {
                $query->where('users.unit_id', $userUnitId);
            }
        }

        // Apply filters
        if ($this->search) {
            $query->where(function($q) {
                $q->where('users.name', 'like', '%' . $this->search . '%')
                  ->orWhere('users.nip', 'like', '%' . $this->search . '%')
                  ->orWhere('users.email', 'like', '%' . $this->search . '%')
                  ->orWhere('users.gelar_depan', 'like', '%' . $this->search . '%')
                  ->orWhere('users.gelar_belakang', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->unit_filter) {
            $query->where('users.unit_id', $this->unit_filter);
        }

        if ($this->position_filter) {
            $query->where('users.position_id', $this->position_filter);
        }

        if ($this->rank_filter) {
            $query->where('users.rank_id', $this->rank_filter);
        }

        // Sort by eselon, rank, and NIP
        $query->leftJoin('positions', 'users.position_id', '=', 'positions.id')
              ->leftJoin('echelons', 'positions.echelon_id', '=', 'echelons.id')
              ->leftJoin('ranks', 'users.rank_id', '=', 'ranks.id')
              // 1. Sort by eselon (lower number = higher eselon)
              ->orderByRaw('CASE WHEN echelons.id IS NULL THEN 999999 ELSE echelons.id END ASC')
              // 2. Sort by rank (higher number = higher rank)
              ->orderByRaw('CASE WHEN ranks.id IS NULL THEN 0 ELSE ranks.id END DESC')
              // 3. Sort by NIP (alphabetical)
              ->orderBy('users.nip', 'ASC')
              ->select('users.*');

        $pegawai = $query->paginate($this->perPage);

        // Get SPT data for the selected month
        $startDate = Carbon::createFromDate($this->selected_year, $this->selected_month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $sptData = Spt::with(['notaDinas.participants.user', 'notaDinas.originPlace', 'notaDinas.destinationCity'])
            ->whereHas('notaDinas', function($q) use ($startDate, $endDate) {
                $q->where(function($q2) use ($startDate, $endDate) {
                    $q2->where('start_date', '<=', $endDate)
                        ->where('end_date', '>=', $startDate);
                });
            })
            ->get();

        // Create schedule data
        $scheduleData = [];
        foreach ($pegawai as $p) {
            $scheduleData[$p->id] = [];
            
            // Get SPTs where this user is a participant
            $userSpts = $sptData->filter(function($spt) use ($p) {
                return $spt->notaDinas->participants->contains('user_id', $p->id);
            });

            foreach ($userSpts as $spt) {
                $startDate = Carbon::parse($spt->notaDinas->start_date)->startOfDay();
                $endDate = Carbon::parse($spt->notaDinas->end_date)->endOfDay();
                
                // Include if any part of the trip is in the selected month
                $tripStartMonth = $startDate->format('Y-m');
                $tripEndMonth = $endDate->format('Y-m');
                $selectedMonth = $this->selected_year . '-' . $this->selected_month;
                
                if ($tripStartMonth === $selectedMonth || $tripEndMonth === $selectedMonth || 
                    ($startDate->lt(Carbon::createFromDate($this->selected_year, $this->selected_month, 1)) && 
                     $endDate->gt(Carbon::createFromDate($this->selected_year, $this->selected_month, 1)->endOfMonth()))) {
                    
                    // Debug: Log the assignment data
                    // \Log::info("Assignment for user {$p->id}: Start={$startDate->format('Y-m-d')}, End={$endDate->format('Y-m-d')}");
                    
                    $scheduleData[$p->id][] = [
                        'spt' => $spt,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'doc_no' => $spt->doc_no,
                        'hal' => $spt->notaDinas->hal,
                        'origin_place' => $spt->notaDinas->originPlace->name ?? '-',
                        'destination_city' => $spt->notaDinas->destinationCity->name ?? '-'
                    ];
                }
            }
        }

        $units = Unit::orderBy('name')->get();
        $positions = Position::orderBy('name')->get();
        $ranks = Rank::orderBy('name')->get();

        // Get days in selected month
        $daysInMonth = Carbon::createFromDate($this->selected_year, $this->selected_month, 1)->daysInMonth;
        $monthName = Carbon::createFromDate($this->selected_year, $this->selected_month, 1)->format('F Y');



        return view('livewire.rekap.pegawai', [
            'pegawai' => $pegawai,
            'units' => $units,
            'positions' => $positions,
            'ranks' => $ranks,
            'scheduleData' => $scheduleData,
            'daysInMonth' => $daysInMonth,
            'monthName' => $monthName,
            'selectedMonth' => $this->selected_month,
            'selectedYear' => $this->selected_year,
        ]);
    }
}
