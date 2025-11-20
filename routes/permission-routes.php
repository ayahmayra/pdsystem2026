<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Permission-based Routes
|--------------------------------------------------------------------------
|
| Routes dengan permission middleware untuk kontrol akses berdasarkan
| roles dan permissions yang sudah didefinisikan.
|
*/

// Documents Routes with Permission Middleware
Route::middleware(['auth', 'permission:documents.view'])->group(function () {
    Route::get('documents', function () {
        return view('documents.main-page');
    })->name('documents');
});

// Nota Dinas Routes with Permission Middleware
Route::middleware(['auth', 'permission:nota-dinas.view', 'unit.scope'])->group(function () {
    Route::get('nota-dinas/{notaDinas}', function(\App\Models\NotaDinas $notaDinas) {
        return redirect()->route('nota-dinas.pdf', $notaDinas);
    })->name('nota-dinas.show');
});

Route::middleware(['auth', 'permission:nota-dinas.create'])->group(function () {
    Route::get('nota-dinas/create', \App\Livewire\NotaDinas\Create::class)->name('nota-dinas.create');
});

Route::middleware(['auth', 'permission:nota-dinas.edit', 'unit.scope'])->group(function () {
    Route::get('nota-dinas/{notaDinas}/edit', \App\Livewire\NotaDinas\Edit::class)->name('nota-dinas.edit');
});

Route::middleware(['auth', 'permission:nota-dinas.view', 'unit.scope'])->group(function () {
    Route::get('nota-dinas/{notaDinas}/pdf', [\App\Http\Controllers\NotaDinasController::class, 'generatePdf'])->name('nota-dinas.pdf');
    Route::get('nota-dinas/{notaDinas}/pdf/download', [\App\Http\Controllers\NotaDinasController::class, 'downloadPdf'])->name('nota-dinas.pdf-download');
});

// SPT Routes with Permission Middleware
Route::middleware(['auth', 'permission:spt.view', 'unit.scope'])->group(function () {
    Route::get('spt/{spt}', function(\App\Models\Spt $spt) {
        return redirect()->route('spt.pdf', $spt);
    })->name('spt.show');
});

Route::middleware(['auth', 'permission:spt.create'])->group(function () {
    Route::get('spt/create', \App\Livewire\Spt\Create::class)->name('spt.create');
});

Route::middleware(['auth', 'permission:spt.edit', 'unit.scope'])->group(function () {
    Route::get('spt/{spt}/edit', \App\Livewire\Spt\Edit::class)->name('spt.edit');
});

Route::middleware(['auth', 'permission:spt.view', 'unit.scope'])->group(function () {
    Route::get('spt/{spt}/pdf', [\App\Http\Controllers\SptController::class, 'generatePdf'])->name('spt.pdf');
    Route::get('spt/{spt}/pdf/download', [\App\Http\Controllers\SptController::class, 'downloadPdf'])->name('spt.pdf-download');
});

// SPPD Routes with Permission Middleware
Route::middleware(['auth', 'permission:sppd.view', 'unit.scope'])->group(function () {
    Route::get('sppd/{sppd}', function(\App\Models\Sppd $sppd) {
        return redirect()->route('sppd.pdf', $sppd);
    })->name('sppd.show');
});

Route::middleware(['auth', 'permission:sppd.create'])->group(function () {
    Route::get('sppd/create', \App\Livewire\Sppd\Create::class)->name('sppd.create');
});

Route::middleware(['auth', 'permission:sppd.edit', 'unit.scope'])->group(function () {
    Route::get('sppd/{sppd}/edit', \App\Livewire\Sppd\Edit::class)->name('sppd.edit');
});

Route::middleware(['auth', 'permission:sppd.view', 'unit.scope'])->group(function () {
    Route::get('sppd/{sppd}/pdf', [\App\Http\Controllers\SppdController::class, 'generatePdf'])->name('sppd.pdf');
    Route::get('sppd/{sppd}/pdf/download', [\App\Http\Controllers\SppdController::class, 'downloadPdf'])->name('sppd.pdf-download');
});

// Receipts Routes with Permission Middleware
Route::middleware(['auth', 'permission:receipts.view', 'unit.scope'])->group(function () {
    Route::get('receipts', \App\Livewire\Receipts\Index::class)->name('receipts.index');
    Route::get('receipts/{receipt}', function(\App\Models\Receipt $receipt) {
        return redirect()->route('receipts.pdf', $receipt);
    })->name('receipts.show');
});

Route::middleware(['auth', 'permission:receipts.create'])->group(function () {
    Route::get('receipts/create', \App\Livewire\Receipts\Create::class)->name('receipts.create');
});

Route::middleware(['auth', 'permission:receipts.edit', 'unit.scope'])->group(function () {
    Route::get('receipts/{receipt}/edit', \App\Livewire\Receipts\Edit::class)->name('receipts.edit');
});

Route::middleware(['auth', 'permission:receipts.view', 'unit.scope'])->group(function () {
    Route::get('receipts/{receipt}/pdf', [\App\Http\Controllers\ReceiptController::class, 'generatePdf'])->name('receipts.pdf');
    Route::get('receipts/{receipt}/pdf/download', [\App\Http\Controllers\ReceiptController::class, 'downloadPdf'])->name('receipts.pdf-download');
});

// Rekapitulasi Routes with Permission Middleware
Route::middleware(['auth', 'permission:rekap.view', 'unit.scope'])->group(function () {
    Route::get('rekap/global', \App\Livewire\Rekap\GlobalRekap::class)->name('rekap.global');
    Route::get('rekap/pegawai', \App\Livewire\Rekap\Pegawai::class)->name('rekap.pegawai');
});

Route::middleware(['auth', 'permission:rekap.export', 'unit.scope'])->group(function () {
    Route::get('rekap/pegawai/pdf', [\App\Http\Controllers\RekapPegawaiController::class, 'generatePdf'])->name('rekap.pegawai.pdf');
});

// Master Data Routes (Admin only)
Route::middleware(['auth', 'permission:master-data.view'])->group(function () {
    // Positions
    Route::get('positions', [\App\Http\Controllers\PositionController::class, 'index'])->name('positions.index');
    Route::get('positions/create', [\App\Http\Controllers\PositionController::class, 'create'])->name('positions.create');
    Route::post('positions', [\App\Http\Controllers\PositionController::class, 'store'])->name('positions.store');
    Route::get('positions/{position}/edit', [\App\Http\Controllers\PositionController::class, 'edit'])->name('positions.edit');
    Route::put('positions/{position}', [\App\Http\Controllers\PositionController::class, 'update'])->name('positions.update');
    Route::delete('positions/{position}', [\App\Http\Controllers\PositionController::class, 'destroy'])->name('positions.destroy');
    
    // Cities
    Route::get('cities', [\App\Http\Controllers\CityController::class, 'index'])->name('cities.index');
    Route::get('cities/create', [\App\Http\Controllers\CityController::class, 'create'])->name('cities.create');
    Route::post('cities', [\App\Http\Controllers\CityController::class, 'store'])->name('cities.store');
    Route::get('cities/{city}/edit', [\App\Http\Controllers\CityController::class, 'edit'])->name('cities.edit');
    Route::put('cities/{city}', [\App\Http\Controllers\CityController::class, 'update'])->name('cities.update');
    Route::delete('cities/{city}', [\App\Http\Controllers\CityController::class, 'destroy'])->name('cities.destroy');
    
    // Districts
    Route::get('districts', [\App\Http\Controllers\DistrictController::class, 'index'])->name('districts.index');
    Route::get('districts/create', [\App\Http\Controllers\DistrictController::class, 'create'])->name('districts.create');
    Route::post('districts', [\App\Http\Controllers\DistrictController::class, 'store'])->name('districts.store');
    Route::get('districts/{district}/edit', [\App\Http\Controllers\DistrictController::class, 'edit'])->name('districts.edit');
    Route::put('districts/{district}', [\App\Http\Controllers\DistrictController::class, 'update'])->name('districts.update');
    Route::delete('districts/{district}', [\App\Http\Controllers\DistrictController::class, 'destroy'])->name('districts.destroy');
    Route::get('districts/cities', [\App\Http\Controllers\DistrictController::class, 'getCities'])->name('districts.cities');
    
    // Provinces
    Route::get('provinces', \App\Livewire\Provinces\Index::class)->name('provinces.index');
    Route::get('provinces/create', \App\Livewire\Provinces\Create::class)->name('provinces.create');
    Route::get('provinces/{province}/edit', \App\Livewire\Provinces\Edit::class)->name('provinces.edit');
    
    // Ranks
    Route::get('ranks', [\App\Http\Controllers\RankController::class, 'index'])->name('ranks.index');
    Route::get('ranks/create', [\App\Http\Controllers\RankController::class, 'create'])->name('ranks.create');
    Route::post('ranks', [\App\Http\Controllers\RankController::class, 'store'])->name('ranks.store');
    Route::get('ranks/{rank}/edit', [\App\Http\Controllers\RankController::class, 'edit'])->name('ranks.edit');
    Route::put('ranks/{rank}', [\App\Http\Controllers\RankController::class, 'update'])->name('ranks.update');
    Route::delete('ranks/{rank}', [\App\Http\Controllers\RankController::class, 'destroy'])->name('ranks.destroy');
    
    // Units
    Route::get('units', [\App\Http\Controllers\UnitController::class, 'index'])->name('units.index');
    Route::get('units/create', [\App\Http\Controllers\UnitController::class, 'create'])->name('units.create');
    Route::post('units', [\App\Http\Controllers\UnitController::class, 'store'])->name('units.store');
    Route::get('units/{unit}/edit', [\App\Http\Controllers\UnitController::class, 'edit'])->name('units.edit');
    Route::put('units/{unit}', [\App\Http\Controllers\UnitController::class, 'update'])->name('units.update');
    Route::delete('units/{unit}', [\App\Http\Controllers\UnitController::class, 'destroy'])->name('units.destroy');
    
    // Travel Grades
    Route::get('travel-grades', \App\Livewire\TravelGrades\Index::class)->name('travel-grades.index');
    Route::get('travel-grades/create', \App\Livewire\TravelGrades\Create::class)->name('travel-grades.create');
    Route::get('travel-grades/{travelGrade}/edit', \App\Livewire\TravelGrades\Edit::class)->name('travel-grades.edit');
    
    // Transport Modes
    Route::get('transport-modes', \App\Livewire\TransportModes\Index::class)->name('transport-modes.index');
    Route::get('transport-modes/create', \App\Livewire\TransportModes\Create::class)->name('transport-modes.create');
    Route::get('transport-modes/{transportMode}/edit', \App\Livewire\TransportModes\Edit::class)->name('transport-modes.edit');
    
    // Travel Routes
    Route::get('travel-routes', \App\Livewire\TravelRoutes\Index::class)->name('travel-routes.index');
    Route::get('travel-routes/create', \App\Livewire\TravelRoutes\Create::class)->name('travel-routes.create');
    Route::get('travel-routes/{travelRoute}/edit', \App\Livewire\TravelRoutes\Edit::class)->name('travel-routes.edit');
    
    // Location Routes
    Route::get('location-routes', \App\Livewire\LocationRoutes\Index::class)->name('location-routes.index');
    
    // Org Places
    Route::get('org-places', \App\Livewire\OrgPlaces\Index::class)->name('org-places.index');
    Route::get('org-places/create', \App\Livewire\OrgPlaces\Create::class)->name('org-places.create');
    Route::get('org-places/{orgPlace}/edit', \App\Livewire\OrgPlaces\Edit::class)->name('org-places.edit');
});

// Reference Rates Routes (Admin only)
Route::middleware(['auth', 'permission:reference-rates.view'])->group(function () {
    Route::get('reference-rates', \App\Livewire\ReferenceRates\Index::class)->name('reference-rates.index');
    
    // Airfare Reference CRUD
    Route::get('airfare-refs', \App\Livewire\AirfareRefs\Index::class)->name('airfare-refs.index');
    Route::get('airfare-refs/create', \App\Livewire\AirfareRefs\Create::class)->name('airfare-refs.create');
    Route::get('airfare-refs/{airfareRef}/edit', \App\Livewire\AirfareRefs\Edit::class)->name('airfare-refs.edit');
    
    // Representation Rate CRUD
    Route::get('representation-rates', \App\Livewire\RepresentationRates\Index::class)->name('representation-rates.index');
    Route::get('representation-rates/create', \App\Livewire\RepresentationRates\Create::class)->name('representation-rates.create');
    Route::get('representation-rates/{representationRate}/edit', \App\Livewire\RepresentationRates\Edit::class)->name('representation-rates.edit');
    
    // Intra Province Transport Reference CRUD
    Route::get('intra-province-transport-refs', \App\Livewire\IntraProvinceTransportRefs\Index::class)->name('intra-province-transport-refs.index');
    Route::get('intra-province-transport-refs/create', \App\Livewire\IntraProvinceTransportRefs\Create::class)->name('intra-province-transport-refs.create');
    Route::get('intra-province-transport-refs/{transportRef}/edit', \App\Livewire\IntraProvinceTransportRefs\Edit::class)->name('intra-province-transport-refs.edit');
    
    // Intra District Transport Reference CRUD
    Route::get('intra-district-transport-refs', \App\Livewire\IntraDistrictTransportRefs\Index::class)->name('intra-district-transport-refs.index');
    Route::get('intra-district-transport-refs/create', \App\Livewire\IntraDistrictTransportRefs\Create::class)->name('intra-district-transport-refs.create');
    Route::get('intra-district-transport-refs/{transportRef}/edit', \App\Livewire\IntraDistrictTransportRefs\Edit::class)->name('intra-district-transport-refs.edit');
    
    // Official Vehicle Transport Reference CRUD
    Route::get('official-vehicle-transport-refs', \App\Livewire\OfficialVehicleTransportRefs\Index::class)->name('official-vehicle-transport-refs.index');
    Route::get('official-vehicle-transport-refs/create', \App\Livewire\OfficialVehicleTransportRefs\Create::class)->name('official-vehicle-transport-refs.create');
    Route::get('official-vehicle-transport-refs/{transportRef}/edit', \App\Livewire\OfficialVehicleTransportRefs\Edit::class)->name('official-vehicle-transport-refs.edit');
    
    // At-Cost Components CRUD
    Route::get('at-cost-components', \App\Livewire\AtCostComponents\Index::class)->name('at-cost-components.index');
    Route::get('at-cost-components/create', \App\Livewire\AtCostComponents\Create::class)->name('at-cost-components.create');
    Route::get('at-cost-components/{component}/edit', \App\Livewire\AtCostComponents\Edit::class)->name('at-cost-components.edit');
    
    // District Perdiem Rates CRUD
    Route::get('district-perdiem-rates', \App\Livewire\DistrictPerdiemRates\Index::class)->name('district-perdiem-rates.index');
    Route::get('district-perdiem-rates/create', \App\Livewire\DistrictPerdiemRates\Create::class)->name('district-perdiem-rates.create');
    Route::get('district-perdiem-rates/{districtPerdiemRate}/edit', \App\Livewire\DistrictPerdiemRates\Edit::class)->name('district-perdiem-rates.edit');
    
    // Perdiem Rates CRUD
    Route::get('perdiem-rates', \App\Livewire\PerdiemRates\Index::class)->name('perdiem-rates.index');
    Route::get('perdiem-rates/create', \App\Livewire\PerdiemRates\Create::class)->name('perdiem-rates.create');
    Route::get('perdiem-rates/{perdiemRate}/edit', \App\Livewire\PerdiemRates\Edit::class)->name('perdiem-rates.edit');
    
    // Lodging Caps CRUD
    Route::get('lodging-caps', \App\Livewire\LodgingCaps\Index::class)->name('lodging-caps.index');
    Route::get('lodging-caps/create', \App\Livewire\LodgingCaps\Create::class)->name('lodging-caps.create');
    Route::get('lodging-caps/{lodgingCap}/edit', \App\Livewire\LodgingCaps\Edit::class)->name('lodging-caps.edit');
});

// User Management Routes (Admin only, except super admin)
Route::middleware(['auth', 'permission:users.view'])->group(function () {
    Route::get('users', \App\Livewire\Users\Index::class)->name('users.index');
});

Route::middleware(['auth', 'permission:users.create'])->group(function () {
    Route::get('users/create', \App\Livewire\Users\Create::class)->name('users.create');
});

Route::middleware(['auth', 'permission:users.edit'])->group(function () {
    Route::get('users/{user}/edit', \App\Livewire\Users\Edit::class)->name('users.edit');
});

// Settings Routes (accessible to all authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::get('settings/profile', \App\Livewire\Settings\Profile::class)->name('profile.show');
    Route::get('settings/password', \App\Livewire\Settings\Password::class)->name('password.show');
    Route::get('settings/appearance', \App\Livewire\Settings\Appearance::class)->name('appearance.show');
    Route::get('settings/delete-user', \App\Livewire\Settings\DeleteUserForm::class)->name('delete-user.show');
});

// Organization Settings (Admin only)
Route::middleware(['auth', 'permission:master-data.edit'])->group(function () {
    Route::get('settings/organization', \App\Livewire\Settings\OrganizationSettings::class)->name('organization.show');
});

// Document Number Formats (Admin only)
Route::middleware(['auth', 'permission:master-data.view'])->group(function () {
    Route::get('doc-number-formats', \App\Livewire\DocNumberFormats\Index::class)->name('doc-number-formats.index');
    Route::get('doc-number-formats/create', \App\Livewire\DocNumberFormats\Create::class)->name('doc-number-formats.create');
    Route::get('doc-number-formats/{format}/edit', \App\Livewire\DocNumberFormats\Edit::class)->name('doc-number-formats.edit');
});

// Supporting Documents (with document permission)
Route::middleware(['auth', 'permission:documents.view', 'unit.scope'])->group(function () {
    Route::get('nota-dinas/{notaDinas}/documents', \App\Livewire\SupportingDocuments\Upload::class)->name('supporting-documents.upload');
    Route::get('nota-dinas/{notaDinas}/documents/{document}/edit', \App\Livewire\SupportingDocuments\Edit::class)->name('supporting-documents.edit');
});
