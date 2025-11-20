<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExamplePermissionController;

/*
|--------------------------------------------------------------------------
| Example Routes with Permission Middleware
|--------------------------------------------------------------------------
|
| This file shows how to implement permission-based routing using
| Spatie Laravel Permission middleware.
|
*/

// Example: Nota Dinas Routes with Permission Middleware
Route::middleware(['auth', 'permission:nota-dinas.view', 'unit.scope'])->group(function () {
    Route::get('/nota-dinas', [ExamplePermissionController::class, 'index'])->name('nota-dinas.index');
});

Route::middleware(['auth', 'permission:nota-dinas.create'])->group(function () {
    Route::get('/nota-dinas/create', [ExamplePermissionController::class, 'create'])->name('nota-dinas.create');
    Route::post('/nota-dinas', [ExamplePermissionController::class, 'store'])->name('nota-dinas.store');
});

Route::middleware(['auth', 'permission:nota-dinas.edit', 'unit.scope'])->group(function () {
    Route::get('/nota-dinas/{notaDinas}/edit', [ExamplePermissionController::class, 'edit'])->name('nota-dinas.edit');
    Route::put('/nota-dinas/{notaDinas}', [ExamplePermissionController::class, 'update'])->name('nota-dinas.update');
});

Route::middleware(['auth', 'permission:nota-dinas.delete', 'unit.scope'])->group(function () {
    Route::delete('/nota-dinas/{notaDinas}', [ExamplePermissionController::class, 'destroy'])->name('nota-dinas.destroy');
});

// Example: SPT Routes with Permission Middleware
Route::middleware(['auth', 'permission:spt.view', 'unit.scope'])->group(function () {
    Route::get('/spt', [ExamplePermissionController::class, 'sptIndex'])->name('spt.index');
});

Route::middleware(['auth', 'permission:spt.create'])->group(function () {
    Route::get('/spt/create', [ExamplePermissionController::class, 'sptCreate'])->name('spt.create');
    Route::post('/spt', [ExamplePermissionController::class, 'sptStore'])->name('spt.store');
});

// Example: SPPD Routes with Permission Middleware
Route::middleware(['auth', 'permission:sppd.view', 'unit.scope'])->group(function () {
    Route::get('/sppd', [ExamplePermissionController::class, 'sppdIndex'])->name('sppd.index');
});

Route::middleware(['auth', 'permission:sppd.create'])->group(function () {
    Route::get('/sppd/create', [ExamplePermissionController::class, 'sppdCreate'])->name('sppd.create');
    Route::post('/sppd', [ExamplePermissionController::class, 'sppdStore'])->name('sppd.store');
});

// Example: Receipts Routes with Permission Middleware
Route::middleware(['auth', 'permission:receipts.view', 'unit.scope'])->group(function () {
    Route::get('/receipts', [ExamplePermissionController::class, 'receiptsIndex'])->name('receipts.index');
});

Route::middleware(['auth', 'permission:receipts.create'])->group(function () {
    Route::get('/receipts/create', [ExamplePermissionController::class, 'receiptsCreate'])->name('receipts.create');
    Route::post('/receipts', [ExamplePermissionController::class, 'receiptsStore'])->name('receipts.store');
});

// Example: Rekapitulasi Routes with Permission Middleware
Route::middleware(['auth', 'permission:rekap.view', 'unit.scope'])->group(function () {
    Route::get('/rekap', [ExamplePermissionController::class, 'rekapIndex'])->name('rekap.index');
});

Route::middleware(['auth', 'permission:rekap.export', 'unit.scope'])->group(function () {
    Route::get('/rekap/export', [ExamplePermissionController::class, 'rekapExport'])->name('rekap.export');
});

// Example: Master Data Routes (Admin only)
Route::middleware(['auth', 'permission:master-data.view'])->group(function () {
    Route::get('/master-data', [ExamplePermissionController::class, 'masterDataIndex'])->name('master-data.index');
});

Route::middleware(['auth', 'permission:master-data.create'])->group(function () {
    Route::get('/master-data/create', [ExamplePermissionController::class, 'masterDataCreate'])->name('master-data.create');
    Route::post('/master-data', [ExamplePermissionController::class, 'masterDataStore'])->name('master-data.store');
});

// Example: User Management Routes (Admin only, except super admin)
Route::middleware(['auth', 'permission:users.view'])->group(function () {
    Route::get('/users', [ExamplePermissionController::class, 'usersIndex'])->name('users.index');
});

Route::middleware(['auth', 'permission:users.create'])->group(function () {
    Route::get('/users/create', [ExamplePermissionController::class, 'usersCreate'])->name('users.create');
    Route::post('/users', [ExamplePermissionController::class, 'usersStore'])->name('users.store');
});

Route::middleware(['auth', 'permission:users.edit'])->group(function () {
    Route::get('/users/{user}/edit', [ExamplePermissionController::class, 'usersEdit'])->name('users.edit');
    Route::put('/users/{user}', [ExamplePermissionController::class, 'usersUpdate'])->name('users.update');
});

Route::middleware(['auth', 'permission:users.delete'])->group(function () {
    Route::delete('/users/{user}', [ExamplePermissionController::class, 'usersDestroy'])->name('users.destroy');
});

// Example: Reference Rates Routes (Admin only)
Route::middleware(['auth', 'permission:reference-rates.view'])->group(function () {
    Route::get('/reference-rates', [ExamplePermissionController::class, 'referenceRatesIndex'])->name('reference-rates.index');
});

Route::middleware(['auth', 'permission:reference-rates.create'])->group(function () {
    Route::get('/reference-rates/create', [ExamplePermissionController::class, 'referenceRatesCreate'])->name('reference-rates.create');
    Route::post('/reference-rates', [ExamplePermissionController::class, 'referenceRatesStore'])->name('reference-rates.store');
});

// Example: Multiple Permission Check
Route::middleware(['auth'])->group(function () {
    // This route requires user to have either 'documents.create' OR 'documents.edit' permission
    Route::get('/documents/manage', function () {
        if (!auth()->user()->canAny(['documents.create', 'documents.edit'])) {
            abort(403, 'Anda tidak memiliki izin untuk mengelola dokumen.');
        }
        return view('documents.manage');
    })->name('documents.manage');
    
    // This route requires user to have both 'rekap.view' AND 'rekap.export' permissions
    Route::get('/rekap/full-access', function () {
        if (!auth()->user()->canAll(['rekap.view', 'rekap.export'])) {
            abort(403, 'Anda tidak memiliki izin penuh untuk rekapitulasi.');
        }
        return view('rekap.full-access');
    })->name('rekap.full-access');
});

// Example: Role-based Access
Route::middleware(['auth'])->group(function () {
    // Only super admin and admin can access this
    Route::get('/admin-panel', function () {
        if (!auth()->user()->hasAnyRole(['super-admin', 'admin'])) {
            abort(403, 'Hanya admin yang dapat mengakses panel ini.');
        }
        return view('admin.panel');
    })->name('admin.panel');
    
    // Only bendahara can access this
    Route::get('/bendahara-panel', function () {
        if (!auth()->user()->hasAnyRole(['bendahara-pengeluaran', 'bendahara-pengeluaran-pembantu'])) {
            abort(403, 'Hanya bendahara yang dapat mengakses panel ini.');
        }
        return view('bendahara.panel');
    })->name('bendahara.panel');
});
