<?php

use App\Livewire\Auth\ConfirmPassword;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Auth\VerifyEmail;

use App\Livewire\Positions\Create as PositionCreate;
use App\Livewire\Positions\Edit as PositionEdit;
use App\Livewire\Positions\Index as PositionIndex;
use App\Livewire\Cities\Create as CityCreate;
use App\Livewire\Cities\Edit as CityEdit;
use App\Livewire\Cities\Index as CityIndex;
use App\Livewire\Districts\Create as DistrictCreate;
use App\Livewire\Districts\Edit as DistrictEdit;
use App\Livewire\Districts\Index as DistrictIndex;
use App\Livewire\OrgPlaces\Create as OrgPlaceCreate;
use App\Livewire\OrgPlaces\Edit as OrgPlaceEdit;
use App\Livewire\OrgPlaces\Index as OrgPlaceIndex;
use App\Livewire\Provinces\Create as ProvinceCreate;
use App\Livewire\Provinces\Edit as ProvinceEdit;
use App\Livewire\Provinces\Index as ProvinceIndex;
use App\Livewire\Ranks\Create as RankCreate;
use App\Livewire\Ranks\Edit as RankEdit;
use App\Livewire\Ranks\Index as RankIndex;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\DeleteUserForm;
use App\Livewire\Settings\OrganizationSettings;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Units\Create as UnitCreate;
use App\Livewire\Units\Edit as UnitEdit;
use App\Livewire\Units\Index as UnitIndex;
use App\Livewire\Users\Create as UserCreate;
use App\Livewire\Users\Edit as UserEdit;
use App\Livewire\Users\Index as UserIndex;
use App\Livewire\TransportModes\Create as TransportModeCreate;
use App\Livewire\TransportModes\Edit as TransportModeEdit;
use App\Livewire\TransportModes\Index as TransportModeIndex;
use App\Livewire\TravelRoutes\Create as TravelRouteCreate;
use App\Livewire\TravelRoutes\Edit as TravelRouteEdit;
use App\Livewire\TravelRoutes\Index as TravelRouteIndex;
use App\Livewire\TravelGrades\Create as TravelGradeCreate;
use App\Livewire\TravelGrades\Edit as TravelGradeEdit;
use App\Livewire\TravelGrades\Index as TravelGradeIndex;

use App\Livewire\PerdiemRates\Create as PerdiemRateCreate;
use App\Livewire\PerdiemRates\Edit as PerdiemRateEdit;
use App\Livewire\PerdiemRates\Index as PerdiemRateIndex;
use App\Livewire\LodgingCaps\Create as LodgingCapCreate;
use App\Livewire\LodgingCaps\Edit as LodgingCapEdit;
use App\Livewire\LodgingCaps\Index as LodgingCapIndex;
use App\Livewire\RepresentationRates\Create as RepresentationRateCreate;
use App\Livewire\RepresentationRates\Edit as RepresentationRateEdit;
use App\Livewire\RepresentationRates\Index as RepresentationRateIndex;
use App\Http\Controllers\ReferenceRatesController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\LocationRoutesController;
use App\Livewire\AirfareRefs\Create as AirfareRefCreate;
use App\Livewire\AirfareRefs\Edit as AirfareRefEdit;
use App\Livewire\AirfareRefs\Index as AirfareRefIndex;
use App\Livewire\IntraProvinceTransportRefs\Create as IntraProvinceTransportRefCreate;
use App\Livewire\IntraProvinceTransportRefs\Edit as IntraProvinceTransportRefEdit;
use App\Livewire\IntraProvinceTransportRefs\Index as IntraProvinceTransportRefIndex;
use App\Livewire\IntraDistrictTransportRefs\Create as IntraDistrictTransportRefCreate;
use App\Livewire\IntraDistrictTransportRefs\Edit as IntraDistrictTransportRefEdit;
use App\Livewire\IntraDistrictTransportRefs\Index as IntraDistrictTransportRefIndex;
use App\Livewire\OfficialVehicleTransportRefs\Create as OfficialVehicleTransportRefCreate;
use App\Livewire\OfficialVehicleTransportRefs\Edit as OfficialVehicleTransportRefEdit;
use App\Livewire\OfficialVehicleTransportRefs\Index as OfficialVehicleTransportRefIndex;
use App\Livewire\AtCostComponents\Create as AtCostComponentCreate;
use App\Livewire\AtCostComponents\Edit as AtCostComponentEdit;
use App\Livewire\AtCostComponents\Index as AtCostComponentIndex;
use App\Livewire\DistrictPerdiemRates\Create as DistrictPerdiemRateCreate;
use App\Livewire\DistrictPerdiemRates\Edit as DistrictPerdiemRateEdit;
use App\Livewire\DistrictPerdiemRates\Index as DistrictPerdiemRateIndex;
use Illuminate\Support\Facades\Route;
use App\Livewire\DocNumberFormats\Index as DocNumberFormatIndex;
use App\Livewire\DocNumberFormats\Create as DocNumberFormatCreate;
use App\Livewire\DocNumberFormats\Edit as DocNumberFormatEdit;
use App\Livewire\NumberSequences\Index as NumberSequenceIndex;
use App\Livewire\DocumentNumbers\Index as DocumentNumberIndex;
use App\Livewire\Spt\Create as SptCreate;
use App\Livewire\Spt\Edit as SptEdit;
use App\Livewire\Sppd\Create as SppdCreate;
use App\Livewire\Sppd\Edit as SppdEdit;
use App\Livewire\Receipts\Create as ReceiptCreate;
use App\Livewire\Receipts\Index as ReceiptIndex;
use App\Livewire\TripReports\Create as TripReportCreate;
use App\Livewire\TripReports\Edit as TripReportEdit;
use App\Livewire\TripReports\Index as TripReportIndex;
use App\Livewire\SupportingDocuments\Upload as SupportingDocumentUpload;
use App\Http\Controllers\NotaDinasController;
use App\Http\Controllers\SptController;
use App\Http\Controllers\SppdController;
use App\Livewire\Rekap\Pegawai as RekapPegawai;
use App\Livewire\Rekap\GlobalRekap;
use App\Livewire\Dashboard;


Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('login', Login::class)->name('login');
    Route::get('register', Register::class)->name('register');
    Route::get('forgot-password', ForgotPassword::class)->name('password.request');
    Route::get('reset-password/{token}', ResetPassword::class)->name('password.reset');
});

Route::middleware(['auth', 'user.role'])->group(function () {
    Route::get('verify-email', VerifyEmail::class)->name('verification.notice');
    Route::get('confirm-password', ConfirmPassword::class)->name('password.confirm');
    Route::get('dashboard', Dashboard::class)->name('dashboard');
    
    // Documentation Page
    Route::get('documentation', function () {
        return view('documentation');
    })->name('documentation');
    
    // Main Documents Page
    Route::get('documents', function () {
        return view('documents.main-page');
    })->name('documents');

    // Master Data Index
    Route::get('master-data', [\App\Http\Controllers\MasterDataController::class, 'index'])->name('master-data.index');

    // Location & Routes Index
    Route::get('location-routes', [LocationRoutesController::class, 'index'])->name('location-routes.index');

    // User CRUD
    Route::get('users', UserIndex::class)->name('users.index');
    Route::get('users/create', UserCreate::class)->name('users.create');
    Route::get('users/{user}/edit', UserEdit::class)->name('users.edit');
                Route::get('users/{user}/permissions', \App\Livewire\Users\ManagePermissions::class)->name('users.permissions');
                Route::get('users/{user}/roles', \App\Livewire\Users\ManageRoles::class)->name('users.roles');
                
                // Role Management Routes
                Route::get('roles', \App\Livewire\Roles\Index::class)->name('roles.index');
                Route::get('roles/{role}/permissions', \App\Livewire\Roles\ManageRolePermissions::class)->name('roles.permissions');

    // Rank CRUD
    Route::get('ranks', [\App\Http\Controllers\RankController::class, 'index'])->name('ranks.index');
    Route::get('ranks/create', [\App\Http\Controllers\RankController::class, 'create'])->name('ranks.create');
    Route::post('ranks', [\App\Http\Controllers\RankController::class, 'store'])->name('ranks.store');
    Route::get('ranks/{rank}/edit', [\App\Http\Controllers\RankController::class, 'edit'])->name('ranks.edit');
    Route::put('ranks/{rank}', [\App\Http\Controllers\RankController::class, 'update'])->name('ranks.update');
    Route::delete('ranks/{rank}', [\App\Http\Controllers\RankController::class, 'destroy'])->name('ranks.destroy');

    // Position CRUD
    Route::get('positions', [\App\Http\Controllers\PositionController::class, 'index'])->name('positions.index');
    Route::get('positions/create', [\App\Http\Controllers\PositionController::class, 'create'])->name('positions.create');
    Route::post('positions', [\App\Http\Controllers\PositionController::class, 'store'])->name('positions.store');
    Route::get('positions/{position}/edit', [\App\Http\Controllers\PositionController::class, 'edit'])->name('positions.edit');
    Route::put('positions/{position}', [\App\Http\Controllers\PositionController::class, 'update'])->name('positions.update');
    Route::delete('positions/{position}', [\App\Http\Controllers\PositionController::class, 'destroy'])->name('positions.destroy');

    // Unit CRUD
    Route::get('units', [\App\Http\Controllers\UnitController::class, 'index'])->name('units.index');
    Route::get('units/create', [\App\Http\Controllers\UnitController::class, 'create'])->name('units.create');
    Route::post('units', [\App\Http\Controllers\UnitController::class, 'store'])->name('units.store');
    Route::get('units/{unit}/edit', [\App\Http\Controllers\UnitController::class, 'edit'])->name('units.edit');
    Route::put('units/{unit}', [\App\Http\Controllers\UnitController::class, 'update'])->name('units.update');
    Route::delete('units/{unit}', [\App\Http\Controllers\UnitController::class, 'destroy'])->name('units.destroy');

    // Sub Kegiatan CRUD
    Route::resource('sub-keg', App\Http\Controllers\SubKegController::class);
    Route::get('sub-keg-import', App\Livewire\SubKeg\ImportExcel::class)->name('sub-keg.import');
    Route::get('sub-keg/{id}/rekening', App\Livewire\SubKeg\Rekening::class)->name('sub-keg.rekening');

    // Province CRUD
    Route::get('provinces', ProvinceIndex::class)->name('provinces.index');
    Route::get('provinces/create', ProvinceCreate::class)->name('provinces.create');
    Route::get('provinces/{province}/edit', ProvinceEdit::class)->name('provinces.edit');

    // City CRUD
    Route::get('cities', [\App\Http\Controllers\CityController::class, 'index'])->name('cities.index');
    Route::get('cities/create', [\App\Http\Controllers\CityController::class, 'create'])->name('cities.create');
    Route::post('cities', [\App\Http\Controllers\CityController::class, 'store'])->name('cities.store');
    Route::get('cities/{city}/edit', [\App\Http\Controllers\CityController::class, 'edit'])->name('cities.edit');
    Route::put('cities/{city}', [\App\Http\Controllers\CityController::class, 'update'])->name('cities.update');
    Route::delete('cities/{city}', [\App\Http\Controllers\CityController::class, 'destroy'])->name('cities.destroy');

    // District CRUD
    Route::get('districts', [\App\Http\Controllers\DistrictController::class, 'index'])->name('districts.index');
    Route::get('districts/create', [\App\Http\Controllers\DistrictController::class, 'create'])->name('districts.create');
    Route::post('districts', [\App\Http\Controllers\DistrictController::class, 'store'])->name('districts.store');
    Route::get('districts/{district}/edit', [\App\Http\Controllers\DistrictController::class, 'edit'])->name('districts.edit');
    Route::put('districts/{district}', [\App\Http\Controllers\DistrictController::class, 'update'])->name('districts.update');
    Route::delete('districts/{district}', [\App\Http\Controllers\DistrictController::class, 'destroy'])->name('districts.destroy');
    Route::get('districts/cities', [\App\Http\Controllers\DistrictController::class, 'getCities'])->name('districts.cities');

    // Org Place CRUD
    Route::get('org-places', OrgPlaceIndex::class)->name('org-places.index');
    Route::get('org-places/create', OrgPlaceCreate::class)->name('org-places.create');
    Route::get('org-places/{orgPlace}/edit', OrgPlaceEdit::class)->name('org-places.edit');

    // Transport Mode CRUD
    Route::get('transport-modes', TransportModeIndex::class)->name('transport-modes.index');
    Route::get('transport-modes/create', TransportModeCreate::class)->name('transport-modes.create');
    Route::get('transport-modes/{transportMode}/edit', TransportModeEdit::class)->name('transport-modes.edit');

    // Travel Route CRUD
    Route::get('travel-routes', TravelRouteIndex::class)->name('travel-routes.index');
    Route::get('travel-routes/create', TravelRouteCreate::class)->name('travel-routes.create');
    Route::get('travel-routes/{travelRoute}/edit', TravelRouteEdit::class)->name('travel-routes.edit');

    // Travel Grade CRUD
    Route::get('travel-grades', TravelGradeIndex::class)->name('travel-grades.index');
    Route::get('travel-grades/create', TravelGradeCreate::class)->name('travel-grades.create');
    Route::get('travel-grades/{travelGrade}/edit', TravelGradeEdit::class)->name('travel-grades.edit');



    // Perdiem Rate CRUD
    Route::get('perdiem-rates', PerdiemRateIndex::class)->name('perdiem-rates.index');
    Route::get('perdiem-rates/create', PerdiemRateCreate::class)->name('perdiem-rates.create');
    Route::get('perdiem-rates/{perdiemRate}/edit', PerdiemRateEdit::class)->name('perdiem-rates.edit');

    // Lodging Cap CRUD
    Route::get('lodging-caps', LodgingCapIndex::class)->name('lodging-caps.index');
    Route::get('lodging-caps/create', LodgingCapCreate::class)->name('lodging-caps.create');
    Route::get('lodging-caps/{lodgingCap}/edit', LodgingCapEdit::class)->name('lodging-caps.edit');

    // Reference Rates Index
    Route::get('reference-rates', [ReferenceRatesController::class, 'index'])->name('reference-rates.index');

    // Airfare Reference CRUD
    Route::get('airfare-refs', AirfareRefIndex::class)->name('airfare-refs.index');
    Route::get('airfare-refs/create', AirfareRefCreate::class)->name('airfare-refs.create');
    Route::get('airfare-refs/{airfareRef}/edit', AirfareRefEdit::class)->name('airfare-refs.edit');

    // Representation Rate CRUD
    Route::get('representation-rates', RepresentationRateIndex::class)->name('representation-rates.index');
    Route::get('representation-rates/create', RepresentationRateCreate::class)->name('representation-rates.create');
    Route::get('representation-rates/{representationRate}/edit', RepresentationRateEdit::class)->name('representation-rates.edit');

    // Intra Province Transport Reference CRUD
    Route::get('intra-province-transport-refs', IntraProvinceTransportRefIndex::class)->name('intra-province-transport-refs.index');
    Route::get('intra-province-transport-refs/create', IntraProvinceTransportRefCreate::class)->name('intra-province-transport-refs.create');
    Route::get('intra-province-transport-refs/{transportRef}/edit', IntraProvinceTransportRefEdit::class)->name('intra-province-transport-refs.edit');

    // Intra District Transport Reference CRUD
    Route::get('intra-district-transport-refs', IntraDistrictTransportRefIndex::class)->name('intra-district-transport-refs.index');
    Route::get('intra-district-transport-refs/create', IntraDistrictTransportRefCreate::class)->name('intra-district-transport-refs.create');
    Route::get('intra-district-transport-refs/{transportRef}/edit', IntraDistrictTransportRefEdit::class)->name('intra-district-transport-refs.edit');

    // Official Vehicle Transport Reference CRUD
    Route::get('official-vehicle-transport-refs', OfficialVehicleTransportRefIndex::class)->name('official-vehicle-transport-refs.index');
    Route::get('official-vehicle-transport-refs/create', OfficialVehicleTransportRefCreate::class)->name('official-vehicle-transport-refs.create');
    Route::get('official-vehicle-transport-refs/{transportRef}/edit', OfficialVehicleTransportRefEdit::class)->name('official-vehicle-transport-refs.edit');

    // At-Cost Components CRUD
    Route::get('at-cost-components', AtCostComponentIndex::class)->name('at-cost-components.index');
    Route::get('at-cost-components/create', AtCostComponentCreate::class)->name('at-cost-components.create');
    Route::get('at-cost-components/{component}/edit', AtCostComponentEdit::class)->name('at-cost-components.edit');

    // District Perdiem Rates CRUD
    Route::get('district-perdiem-rates', DistrictPerdiemRateIndex::class)->name('district-perdiem-rates.index');
    Route::get('district-perdiem-rates/create', DistrictPerdiemRateCreate::class)->name('district-perdiem-rates.create');
    Route::get('district-perdiem-rates/{districtPerdiemRate}/edit', DistrictPerdiemRateEdit::class)->name('district-perdiem-rates.edit');

    // Settings
    Route::get('settings/profile', Profile::class)->name('profile.show');
    Route::get('settings/password', Password::class)->name('password.show');
    Route::get('settings/appearance', Appearance::class)->name('appearance.show');
    Route::get('settings/delete-user', DeleteUserForm::class)->name('delete-user.show');
    Route::get('settings/organization', OrganizationSettings::class)->name('organization.show');

    // Format Penomoran Dokumen CRUD
    Route::get('doc-number-formats', DocNumberFormatIndex::class)->name('doc-number-formats.index');
    Route::get('doc-number-formats/create', DocNumberFormatCreate::class)->name('doc-number-formats.create');
    Route::get('doc-number-formats/{format}/edit', DocNumberFormatEdit::class)->name('doc-number-formats.edit');

    // Number Sequence
    Route::get('number-sequences', NumberSequenceIndex::class)->name('number-sequences.index');
    // Document Numbers (Audit Trail)
    Route::get('document-numbers', DocumentNumberIndex::class)->name('document-numbers.index');

    // Nota Dinas CRUD
    Route::get('nota-dinas/create', \App\Livewire\NotaDinas\Create::class)->name('nota-dinas.create');
    Route::get('nota-dinas/{notaDinas}/edit', \App\Livewire\NotaDinas\Edit::class)->name('nota-dinas.edit');
    Route::get('nota-dinas/{notaDinas}', function(\App\Models\NotaDinas $notaDinas) {
        return redirect()->route('nota-dinas.pdf', $notaDinas);
    })->name('nota-dinas.show');

    // SPT CRUD
    Route::get('spt/create', SptCreate::class)->name('spt.create');
    Route::get('spt/{spt}/edit', SptEdit::class)->name('spt.edit');
    // Redirect show SPT langsung ke PDF
    Route::get('spt/{spt}', function(\App\Models\Spt $spt) {
        return redirect()->route('spt.pdf', $spt);
    })->name('spt.show');

    // SPPD CRUD
    Route::get('sppd/create', SppdCreate::class)->name('sppd.create');
    Route::get('sppd/{sppd}/edit', SppdEdit::class)->name('sppd.edit');

    // Receipts CRUD
    Route::get('receipts', ReceiptIndex::class)->name('receipts.index');
    Route::get('receipts/create', ReceiptCreate::class)->name('receipts.create');
    Route::get('receipts/{receipt}/edit', \App\Livewire\Receipts\Edit::class)->name('receipts.edit');
    
    // Receipts PDF Routes
    Route::get('receipts/{receipt}/pdf', [App\Http\Controllers\ReceiptController::class, 'generatePdf'])->name('receipts.pdf');
    Route::get('receipts/{receipt}/pdf/download', [App\Http\Controllers\ReceiptController::class, 'downloadPdf'])->name('receipts.pdf-download');
    
    // Redirect show SPPD langsung ke PDF
    Route::get('receipts/{receipt}', function(\App\Models\Receipt $receipt) {
        return redirect()->route('receipts.pdf', $receipt);
    })->name('receipts.show');

    // Trip Reports CRUD
    Route::get('trip-reports', TripReportIndex::class)->name('trip-reports.index');
Route::get('trip-reports/create', TripReportCreate::class)->name('trip-reports.create');
Route::get('trip-reports/{tripReport}/edit', TripReportEdit::class)->name('trip-reports.edit');
Route::get('trip-reports/{tripReport}/pdf', [App\Http\Controllers\TripReportController::class, 'pdf'])->name('trip-reports.pdf');

    // Supporting Documents
    Route::get('nota-dinas/{notaDinas}/documents', SupportingDocumentUpload::class)->name('supporting-documents.upload');
    Route::get('nota-dinas/{notaDinas}/documents/{document}/edit', \App\Livewire\SupportingDocuments\Edit::class)->name('supporting-documents.edit');
    // SPPD show route is now handled in permission-routes.php with proper unit scope middleware
    
    // Nota Dinas PDF routes are now handled in permission-routes.php with proper unit scope middleware

    // Rekapitulasi Routes
    Route::get('rekap/global', GlobalRekap::class)->name('rekap.global');
    Route::get('rekap/pegawai', RekapPegawai::class)->name('rekap.pegawai');
    Route::get('rekap/pegawai/pdf', [\App\Http\Controllers\RekapPegawaiController::class, 'generatePdf'])->name('rekap.pegawai.pdf');

// SPT and SPPD PDF routes are now handled in permission-routes.php with proper unit scope middleware
});

require __DIR__.'/permission-routes.php';
require __DIR__.'/auth.php';
