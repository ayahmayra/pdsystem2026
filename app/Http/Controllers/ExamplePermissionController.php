<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\PermissionHelper;
use App\Models\NotaDinas;
use App\Models\Receipt;

class ExamplePermissionController extends Controller
{
    /**
     * Example: Nota Dinas Controller with Permission Checks
     */
    public function index(Request $request)
    {
        // Check if user can view nota dinas
        if (!PermissionHelper::can('nota-dinas.view')) {
            abort(403, 'Anda tidak memiliki izin untuk melihat nota dinas.');
        }

        $query = NotaDinas::with(['participants.user', 'requestingUnit', 'destinationCity']);

        // Apply unit scope filtering for bendahara pengeluaran pembantu
        if (!PermissionHelper::canAccessAllData()) {
            $userUnitId = PermissionHelper::getUserUnitId();
            if ($userUnitId) {
                $query->where('requesting_unit_id', $userUnitId);
            }
        }

        $notaDinas = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('nota-dinas.index', compact('notaDinas'));
    }

    public function create()
    {
        // Check if user can create nota dinas
        if (!PermissionHelper::can('nota-dinas.create')) {
            abort(403, 'Anda tidak memiliki izin untuk membuat nota dinas.');
        }

        return view('nota-dinas.create');
    }

    public function store(Request $request)
    {
        // Check if user can create nota dinas
        if (!PermissionHelper::can('nota-dinas.create')) {
            abort(403, 'Anda tidak memiliki izin untuk membuat nota dinas.');
        }

        // Validation and store logic here
        // ...

        return redirect()->route('nota-dinas.index')
            ->with('success', 'Nota dinas berhasil dibuat.');
    }

    public function edit(NotaDinas $notaDinas)
    {
        // Check if user can edit nota dinas
        if (!PermissionHelper::can('nota-dinas.edit')) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit nota dinas.');
        }

        // Check unit scope for bendahara pengeluaran pembantu
        if (!PermissionHelper::canAccessAllData()) {
            $userUnitId = PermissionHelper::getUserUnitId();
            if ($userUnitId && $notaDinas->requesting_unit_id != $userUnitId) {
                abort(403, 'Anda hanya dapat mengedit nota dinas dari bidang Anda.');
            }
        }

        return view('nota-dinas.edit', compact('notaDinas'));
    }

    public function update(Request $request, NotaDinas $notaDinas)
    {
        // Check if user can edit nota dinas
        if (!PermissionHelper::can('nota-dinas.edit')) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit nota dinas.');
        }

        // Check unit scope for bendahara pengeluaran pembantu
        if (!PermissionHelper::canAccessAllData()) {
            $userUnitId = PermissionHelper::getUserUnitId();
            if ($userUnitId && $notaDinas->requesting_unit_id != $userUnitId) {
                abort(403, 'Anda hanya dapat mengedit nota dinas dari bidang Anda.');
            }
        }

        // Validation and update logic here
        // ...

        return redirect()->route('nota-dinas.index')
            ->with('success', 'Nota dinas berhasil diperbarui.');
    }

    public function destroy(NotaDinas $notaDinas)
    {
        // Check if user can delete nota dinas
        if (!PermissionHelper::can('nota-dinas.delete')) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus nota dinas.');
        }

        // Check unit scope for bendahara pengeluaran pembantu
        if (!PermissionHelper::canAccessAllData()) {
            $userUnitId = PermissionHelper::getUserUnitId();
            if ($userUnitId) {
                $hasAccess = $notaDinas->participants()
                    ->where('user_unit_id_snapshot', $userUnitId)
                    ->exists();
                
                if (!$hasAccess) {
                    abort(403, 'Anda hanya dapat menghapus nota dinas dari bidang Anda.');
                }
            }
        }

        $notaDinas->delete();

        return redirect()->route('nota-dinas.index')
            ->with('success', 'Nota dinas berhasil dihapus.');
    }

    /**
     * Example: Rekapitulasi Controller with Permission Checks
     */
    public function rekapIndex(Request $request)
    {
        // Check if user can view rekapitulasi
        if (!PermissionHelper::canViewRekap()) {
            abort(403, 'Anda tidak memiliki izin untuk melihat rekapitulasi.');
        }

        $query = Receipt::with(['sppd.spt.notaDinas', 'payeeUser']);

        // Apply unit scope filtering for bendahara pengeluaran pembantu and sekretariat
        if (!PermissionHelper::canAccessAllData()) {
            $userUnitId = PermissionHelper::getUserUnitId();
            if ($userUnitId) {
                $query->whereHas('sppd.spt.notaDinas', function ($q) use ($userUnitId) {
                    $q->where('requesting_unit_id', $userUnitId);
                });
            }
        }

        $receipts = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('rekap.index', compact('receipts'));
    }

    public function rekapExport(Request $request)
    {
        // Check if user can export rekapitulasi
        if (!PermissionHelper::canExportRekap()) {
            abort(403, 'Anda tidak memiliki izin untuk mengekspor rekapitulasi.');
        }

        // Export logic here
        // ...

        return response()->download($exportPath);
    }
}
