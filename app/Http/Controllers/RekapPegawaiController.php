<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Unit;
use App\Models\Position;
use App\Models\Rank;
use App\Models\Spt;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\PermissionHelper;

class RekapPegawaiController extends Controller
{
    /**
     * Display the rekap pegawai page.
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $search = $request->get('search', '');
        $unitFilter = $request->get('unit_filter', '');
        $positionFilter = $request->get('position_filter', '');
        $rankFilter = $request->get('rank_filter', '');
        $selectedMonth = $request->get('selected_month', now()->format('m'));
        $selectedYear = $request->get('selected_year', now()->format('Y'));
        $perPage = $request->get('per_page', 10);

        // Build query
        $query = User::with([
            'unit',
            'position.echelon',
            'rank',
            'travelGrade'
        ])->where('is_non_staff', false); // Exclude non-staff users

        // Apply filters
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('nip', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('gelar_depan', 'like', '%' . $search . '%')
                  ->orWhere('gelar_belakang', 'like', '%' . $search . '%');
            });
        }

        if ($unitFilter) {
            $query->where('unit_id', $unitFilter);
        }

        if ($positionFilter) {
            $query->where('position_id', $positionFilter);
        }

        if ($rankFilter) {
            $query->where('rank_id', $rankFilter);
        }

        // Sort by eselon, rank, and NIP
        $query->leftJoin('positions', 'users.position_id', '=', 'positions.id')
              ->leftJoin('echelons', 'positions.echelon_id', '=', 'echelons.id')
              ->leftJoin('ranks', 'users.rank_id', '=', 'ranks.id')
              // 1. Sort by eselon (lower number = higher eselon)
              ->orderByRaw('CASE WHEN echelons.id IS NULL THEN 999999 ELSE echelons.id END ASC')
              // 2. Sort by rank (higher number = higher rank)
              ->orderByRaw('CASE WHEN ranks.id IS NULL THEN 0 ELSE ranks.id END ASC')
              // 3. Sort by NIP (alphabetical)
              ->orderBy('users.nip', 'DESC')
              ->select('users.*');

        $pegawai = $query->paginate($perPage);

        // Get SPT data for the selected month
        $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth();
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
                $selectedMonthStr = $selectedYear . '-' . $selectedMonth;
                
                if ($tripStartMonth === $selectedMonthStr || $tripEndMonth === $selectedMonthStr || 
                    ($startDate->lt(Carbon::createFromDate($selectedYear, $selectedMonth, 1)) && 
                     $endDate->gt(Carbon::createFromDate($selectedYear, $selectedMonth, 1)->endOfMonth()))) {
                    
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

        // Get filter options
        $units = Unit::orderBy('name')->get();
        $positions = Position::orderBy('name')->get();
        $ranks = Rank::orderBy('name')->get();

        // Get days in selected month
        $daysInMonth = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->daysInMonth;
        
        // Convert month name to Indonesian
        $monthNames = [
            'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
            'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
            'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
            'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
        ];
        $englishMonthName = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->format('F');
        $monthName = $monthNames[$englishMonthName] . ' ' . $selectedYear;

        return view('rekap.pegawai', [
            'pegawai' => $pegawai,
            'units' => $units,
            'positions' => $positions,
            'ranks' => $ranks,
            'scheduleData' => $scheduleData,
            'daysInMonth' => $daysInMonth,
            'monthName' => $monthName,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'search' => $search,
            'unitFilter' => $unitFilter,
            'positionFilter' => $positionFilter,
            'rankFilter' => $rankFilter,
            'perPage' => $perPage,
        ]);
    }

    /**
     * Generate PDF for rekap pegawai.
     */
    public function generatePdf(Request $request)
    {
        // Check permission
        if (!PermissionHelper::can('rekap.view')) {
            abort(403, 'Anda tidak memiliki izin untuk melihat rekapitulasi.');
        }

        // Get filter parameters (same as index method)
        $search = $request->get('search', '');
        $unitFilter = $request->get('unit_filter', '');
        $positionFilter = $request->get('position_filter', '');
        $rankFilter = $request->get('rank_filter', '');
        $selectedMonth = $request->get('selected_month', now()->format('m'));
        $selectedYear = $request->get('selected_year', now()->format('Y'));

        // Build query (same logic as index method but without pagination)
        $query = User::with([
            'unit',
            'position.echelon',
            'rank',
            'travelGrade'
        ])->where('is_non_staff', false); // Exclude non-staff users

        // Apply unit scope filtering for bendahara pengeluaran pembantu
        if (!PermissionHelper::canAccessAllData()) {
            $userUnitId = PermissionHelper::getUserUnitId();
            if ($userUnitId) {
                $query->where('users.unit_id', $userUnitId);
            }
        }

        // Apply filters
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('users.name', 'like', '%' . $search . '%')
                  ->orWhere('users.nip', 'like', '%' . $search . '%')
                  ->orWhere('users.email', 'like', '%' . $search . '%')
                  ->orWhere('users.gelar_depan', 'like', '%' . $search . '%')
                  ->orWhere('users.gelar_belakang', 'like', '%' . $search . '%');
            });
        }

        if ($unitFilter) {
            $query->where('users.unit_id', $unitFilter);
        }

        if ($positionFilter) {
            $query->where('users.position_id', $positionFilter);
        }

        if ($rankFilter) {
            $query->where('users.rank_id', $rankFilter);
        }

        // Sort by eselon, rank, and NIP
        $query->leftJoin('positions', 'users.position_id', '=', 'positions.id')
              ->leftJoin('echelons', 'positions.echelon_id', '=', 'echelons.id')
              ->leftJoin('ranks', 'users.rank_id', '=', 'ranks.id')
              ->orderByRaw('CASE WHEN echelons.id IS NULL THEN 999999 ELSE echelons.id END ASC')
              ->orderByRaw('CASE WHEN ranks.id IS NULL THEN 0 ELSE ranks.id END ASC')
              ->orderBy('users.nip', 'DESC')
              ->select('users.*');

        // Get all data without pagination for PDF
        $pegawai = $query->get();

        // Get SPT data for the selected month (same logic as index)
        $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $sptData = Spt::with(['notaDinas.participants.user', 'notaDinas.originPlace', 'notaDinas.destinationCity'])
            ->whereHas('notaDinas', function($q) use ($startDate, $endDate) {
                $q->where(function($q2) use ($startDate, $endDate) {
                    $q2->where('start_date', '<=', $endDate)
                        ->where('end_date', '>=', $startDate);
                });
            })
            ->get();

        // Create schedule data (same logic as index)
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
                $selectedMonthStr = $selectedYear . '-' . $selectedMonth;
                
                if ($tripStartMonth === $selectedMonthStr || $tripEndMonth === $selectedMonthStr || 
                    ($startDate->lt(Carbon::createFromDate($selectedYear, $selectedMonth, 1)) && 
                     $endDate->gt(Carbon::createFromDate($selectedYear, $selectedMonth, 1)->endOfMonth()))) {
                    
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

        // Get days in selected month
        $daysInMonth = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->daysInMonth;
        
        // Convert month name to Indonesian
        $monthNames = [
            'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
            'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
            'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
            'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
        ];
        $englishMonthName = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->format('F');
        $monthName = $monthNames[$englishMonthName] . ' ' . $selectedYear;

        // Generate PDF
        $pdf = Pdf::loadView('rekap.pegawai.pdf', [
            'pegawai' => $pegawai,
            'scheduleData' => $scheduleData,
            'daysInMonth' => $daysInMonth,
            'monthName' => $monthName,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
        ]);
        
        // Set paper size dan orientation
        $pdf->setPaper('A4', 'landscape'); // Landscape untuk tabel yang lebar
        
        // Set options untuk hasil yang lebih baik
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Arial',
            'chroot' => public_path(),
        ]);
        
        // Generate filename
        $filename = 'rekap-pegawai-' . $selectedYear . '-' . str_pad($selectedMonth, 2, '0', STR_PAD_LEFT) . '.pdf';
        
        // Return PDF stream
        return $pdf->stream($filename);
    }
}