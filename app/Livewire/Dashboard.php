<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\NotaDinas;
use App\Models\Spt;
use App\Models\Sppd;
use App\Models\Receipt;
use App\Helpers\PermissionHelper;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('components.layouts.app', ['title' => 'Dashboard'])]
class Dashboard extends Component
{
    public function render()
    {
        // Get current user and permissions
        $currentUser = \Illuminate\Support\Facades\Auth::user();
        $canAccessAllData = PermissionHelper::canAccessAllData();
        $userUnitId = PermissionHelper::getUserUnitId();

        // 1. STATISTIK UTAMA (Key Metrics)
        $stats = $this->getMainStatistics($canAccessAllData, $userUnitId);

        // 2. STATUS DOKUMEN (Document Status)
        $documentStatus = $this->getDocumentStatus($canAccessAllData, $userUnitId);

        // 3. AKTIVITAS TERBARU (Recent Activities)
        $recentActivities = $this->getRecentActivities($canAccessAllData, $userUnitId);

        // 4. QUICK ACTIONS (Available based on permissions)
        $quickActions = $this->getQuickActions();

        return view('livewire.dashboard', [
            'stats' => $stats,
            'documentStatus' => $documentStatus,
            'recentActivities' => $recentActivities,
            'quickActions' => $quickActions,
            'currentUser' => $currentUser,
        ]);
    }

    private function getMainStatistics($canAccessAllData, $userUnitId)
    {
        $baseQuery = NotaDinas::query();
        $userQuery = User::query();
        $sptQuery = Spt::query();
        $sppdQuery = Sppd::query();

        // Apply unit scope filtering if user can't access all data
        if (!$canAccessAllData && $userUnitId) {
            $baseQuery->where('requesting_unit_id', $userUnitId);
            $userQuery->where('unit_id', $userUnitId);
            $sptQuery->whereHas('notaDinas', function($q) use ($userUnitId) {
                $q->where('requesting_unit_id', $userUnitId);
            });
            $sppdQuery->whereHas('spt.notaDinas', function($q) use ($userUnitId) {
                $q->where('requesting_unit_id', $userUnitId);
            });
        }

        // Filter out non-staff users
        $userQuery->where('is_non_staff', false);

        return [
            'total_nota_dinas' => $baseQuery->count(),
            'total_nota_dinas_month' => $baseQuery->whereMonth('created_at', now()->month)
                                                ->whereYear('created_at', now()->year)
                                                ->count(),
            'total_pegawai' => $userQuery->count(),
            'total_spt' => $sptQuery->count(),
            'total_spt_month' => $sptQuery->whereMonth('created_at', now()->month)
                                         ->whereYear('created_at', now()->year)
                                         ->count(),
            'total_sppd' => $sppdQuery->count(),
            'total_sppd_month' => $sppdQuery->whereMonth('created_at', now()->month)
                                           ->whereYear('created_at', now()->year)
                                           ->count(),
            'total_perjalanan_aktif' => $sppdQuery->count(), // SPPD tidak memiliki status field
        ];
    }

    private function getDocumentStatus($canAccessAllData, $userUnitId)
    {
        $baseQuery = NotaDinas::query();
        $sptQuery = Spt::query();
        $sppdQuery = Sppd::query();

        // Apply unit scope filtering if user can't access all data
        if (!$canAccessAllData && $userUnitId) {
            $baseQuery->where('requesting_unit_id', $userUnitId);
            $sptQuery->whereHas('notaDinas', function($q) use ($userUnitId) {
                $q->where('requesting_unit_id', $userUnitId);
            });
            $sppdQuery->whereHas('spt.notaDinas', function($q) use ($userUnitId) {
                $q->where('requesting_unit_id', $userUnitId);
            });
        }

        return [
            'nota_dinas_pending' => $baseQuery->where('status', 'pending')->count(),
            'nota_dinas_approved' => $baseQuery->where('status', 'approved')->count(),
            'nota_dinas_rejected' => $baseQuery->where('status', 'rejected')->count(),
            'spt_aktif' => $sptQuery->count(), // SPT tidak memiliki status field
            'spt_selesai' => 0, // SPT tidak memiliki status field
            'sppd_aktif' => $sppdQuery->count(), // SPPD tidak memiliki status field
            'sppd_selesai' => 0, // SPPD tidak memiliki status field
            'dokumen_overdue' => $this->getOverdueDocuments($canAccessAllData, $userUnitId),
        ];
    }

    private function getOverdueDocuments($canAccessAllData, $userUnitId)
    {
        $query = NotaDinas::query()
            ->where('status', 'pending')
            ->where('created_at', '<', now()->subDays(7)); // Overdue if pending for more than 7 days

        if (!$canAccessAllData && $userUnitId) {
            $query->where('requesting_unit_id', $userUnitId);
        }

        return $query->count();
    }

    private function getRecentActivities($canAccessAllData, $userUnitId)
    {
        $activities = collect();

        // Recent Nota Dinas
        $notaDinasQuery = NotaDinas::with(['fromUser', 'toUser', 'requestingUnit'])
            ->orderBy('created_at', 'desc')
            ->limit(5);

        if (!$canAccessAllData && $userUnitId) {
            $notaDinasQuery->where('requesting_unit_id', $userUnitId);
        }

        $recentNotaDinas = $notaDinasQuery->get();
        foreach ($recentNotaDinas as $nota) {
            $activities->push([
                'type' => 'nota_dinas',
                'title' => 'Nota Dinas Baru',
                'description' => "Dari {$nota->fromUser->name} ke {$nota->toUser->name}",
                'doc_no' => $nota->doc_no,
                'status' => $nota->status,
                'created_at' => $nota->created_at,
                'url' => route('nota-dinas.show', $nota->id),
            ]);
        }

        // Recent SPT
        $sptQuery = Spt::with(['notaDinas.fromUser', 'notaDinas.toUser', 'notaDinas.requestingUnit'])
            ->orderBy('created_at', 'desc')
            ->limit(5);

        if (!$canAccessAllData && $userUnitId) {
            $sptQuery->whereHas('notaDinas', function($q) use ($userUnitId) {
                $q->where('requesting_unit_id', $userUnitId);
            });
        }

        $recentSpt = $sptQuery->get();
        foreach ($recentSpt as $spt) {
            $activities->push([
                'type' => 'spt',
                'title' => 'SPT Baru',
                'description' => "SPT untuk {$spt->notaDinas->fromUser->name}",
                'doc_no' => $spt->doc_no,
                'status' => 'aktif', // SPT tidak memiliki status field, default ke aktif
                'created_at' => $spt->created_at,
                'url' => route('spt.show', $spt->id),
            ]);
        }

        // Recent SPPD
        $sppdQuery = Sppd::with(['spt.notaDinas.fromUser', 'spt.notaDinas.toUser', 'spt.notaDinas.requestingUnit'])
            ->orderBy('created_at', 'desc')
            ->limit(5);

        if (!$canAccessAllData && $userUnitId) {
            $sppdQuery->whereHas('spt.notaDinas', function($q) use ($userUnitId) {
                $q->where('requesting_unit_id', $userUnitId);
            });
        }

        $recentSppd = $sppdQuery->get();
        foreach ($recentSppd as $sppd) {
            $activities->push([
                'type' => 'sppd',
                'title' => 'SPPD Baru',
                'description' => "SPPD untuk {$sppd->spt->notaDinas->fromUser->name}",
                'doc_no' => $sppd->doc_no,
                'status' => 'aktif', // SPPD tidak memiliki status field, default ke aktif
                'created_at' => $sppd->created_at,
                'url' => route('sppd.show', $sppd->id),
            ]);
        }

        // Sort by created_at and take top 10
        return $activities->sortByDesc('created_at')->take(10)->values();
    }

    private function getQuickActions()
    {
        $actions = [];

        // Create Nota Dinas
        if (PermissionHelper::can('documents.create')) {
            $actions[] = [
                'title' => 'Buat Nota Dinas',
                'description' => 'Buat dokumen nota dinas baru',
                'icon' => 'document-text',
                'color' => 'blue',
                'url' => route('nota-dinas.create'),
            ];
        }

        // Create SPT
        // if (PermissionHelper::can('documents.create')) {
        //     $actions[] = [
        //         'title' => 'Buat SPT',
        //         'description' => 'Buat Surat Perintah Tugas',
        //         'icon' => 'document-duplicate',
        //         'color' => 'green',
        //         'url' => route('spt.create'),
        //     ];
        // }

        // View Rekap Pegawai
        if (PermissionHelper::can('rekap.view')) {
            $actions[] = [
                'title' => 'Rekap Pegawai',
                'description' => 'Lihat rekap perjalanan pegawai',
                'icon' => 'users',
                'color' => 'purple',
                'url' => route('rekap.pegawai'),
            ];
        }

        // Master Data
        if (PermissionHelper::can('master-data.view')) {
            $actions[] = [
                'title' => 'Master Data',
                'description' => 'Kelola data master sistem',
                'icon' => 'cog-6-tooth',
                'color' => 'gray',
                'url' => route('master-data.index'),
            ];
        }

        // User Management
        if (PermissionHelper::can('users.view')) {
            $actions[] = [
                'title' => 'Data Pegawai',
                'description' => 'Kelola data pegawai',
                'icon' => 'user-group',
                'color' => 'indigo',
                'url' => route('users.index'),
            ];
        }

        // Documentation
        $actions[] = [
            'title' => 'Dokumentasi',
            'description' => 'Lihat dokumentasi sistem',
            'icon' => 'book-open',
            'color' => 'yellow',
            'url' => route('documentation'),
        ];

        return $actions;
    }
}
